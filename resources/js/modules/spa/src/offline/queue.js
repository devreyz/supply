/**
 * 📋 SPA Framework - Job Queue
 * Sistema de filas e jobs para sincronização offline
 */

/**
 * Status de um job
 */
export const JobStatus = {
    PENDING: "pending",
    PROCESSING: "processing",
    COMPLETED: "completed",
    FAILED: "failed",
    CANCELLED: "cancelled",
};

/**
 * Gerenciador de filas de jobs
 */
class JobQueue {
    constructor(db) {
        this.db = db;
        this.storeName = "_queue";
        this.handlers = new Map();
        this.actions = this.handlers; // Alias para semântica de ações
        this.isProcessing = false;
        this.retryAttempts = 3;
        this.retryDelay = 1000;
        this._listeners = new Map();
    }

    /**
     * Inicializa a queue
     */
    async init() {
        console.log("📋 Job Queue inicializada");

        // Limpeza de jobs que ficaram travados em "processing" (ex: fechamento do browser)
        await this._cleanupStuckJobs();

        // Listeners para reconexão
        if (typeof window !== "undefined") {
            window.addEventListener("online", () => {
                console.log("🌐 Conexão restaurada. Processando fila...");
                this.processNext();
            });

            // Listener para mensagens do Service Worker
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker.addEventListener("message", (event) => {
                    if (event.data && event.data.type === "SYNC_TRIGGERED") {
                        console.log("🔄 Sync disparado pelo SW");
                        this.processNext();
                    }
                });
            }

            // Verifica quando a aba volta a ficar visível
            document.addEventListener("visibilitychange", () => {
                if (document.visibilityState === "visible") {
                    this.processNext();
                }
            });
        }

        // Heartbeat periódico para jobs agendados
        setInterval(() => this.processNext(), 30000);

        // Processa imediatamente se online
        if (navigator.onLine) {
            this.processNext();
        }

        return this;
    }

    /**
     * Recupera jobs que ficaram travados em processamento
     */
    async _cleanupStuckJobs() {
        const stuck = await this.processing();
        for (const job of stuck) {
            console.warn(`📋 Recuperando job travado: #${job.id}`);
            await this.db.table(this.storeName).update(job.id, {
                status: JobStatus.PENDING,
                error: "Interrompido (Browser fechado ou erro fatal)",
                updatedAt: new Date().toISOString(),
            });
        }
    }

    /**
     * Registra uma ação (handler) para um tipo de job
     * @param {string} name - Nome da ação/identificador
     * @param {Function} callback - Função que processa a ação
     */
    defineAction(name, callback) {
        this.handlers.set(name, callback);
        console.log(`📋 Ação registrada: ${name}`);
        return this;
    }

    /**
     * Registra múltiplas ações de uma vez
     * @param {Object} actions - Objeto com nome: callback
     */
    defineActions(actions) {
        Object.entries(actions).forEach(([name, callback]) => {
            this.defineAction(name, callback);
        });
        return this;
    }

    /**
     * Registra um handler para um tipo de job (Legado)
     * @param {string} type - Tipo do job
     * @param {Function} handler - Função que processa o job
     */
    register(type, handler) {
        return this.defineAction(type, handler);
    }

    /**
     * Despacha uma nova ação para a fila
     * @param {string} action - Nome da ação
     * @param {Object} payload - Dados da ação
     * @param {Object} options - Opções (priority, delay, deleteOnComplete)
     */
    async dispatch(action, payload = {}, options = {}) {
        return this.add(action, payload, options);
    }

    /**
     * Adiciona um job à fila
     * @param {string} type - Tipo do job
     * @param {Object} data - Dados do job
     * @param {Object} options - Opções adicionais
     */
    async add(type, data, options = {}) {
        // Prevenção de duplicatas se solicitado
        if (options.unique) {
            const existing = await this.db
                .table(this.storeName)
                .where((j) => j.type === type && j.status === JobStatus.PENDING)
                .first();

            if (
                existing &&
                JSON.stringify(existing.data) === JSON.stringify(data)
            ) {
                console.log(`📋 Job duplicado ignorado: ${type}`);
                return existing.id;
            }
        }

        const job = {
            type,
            data,
            status: JobStatus.PENDING,
            attempts: 0,
            maxAttempts: options.maxAttempts || this.retryAttempts,
            priority: options.priority || 0,
            delay: options.delay || 0,
            timeout: options.timeout || 0, // Tempo máximo de execução em ms
            deleteOnComplete: options.deleteOnComplete !== false, // Default: true
            createdAt: new Date().toISOString(),
            scheduledAt: options.delay
                ? new Date(Date.now() + options.delay).toISOString()
                : new Date().toISOString(),
            error: null,
            result: null,
            progress: 0,
        };

        const result = await this.db.table(this.storeName).insert(job);

        this._emit("job:added", result);
        console.log(`📋 Job adicionado: ${type} #${result.id}`);

        // Processa imediatamente se online e sem delay
        if (navigator.onLine && !options.delay) {
            this.processNext();
        }

        return result.id;
    }

    /**
     * Atualiza o progresso de um job
     * @param {number} id - ID do job
     * @param {number} progress - Progresso (0-100)
     */
    async setProgress(id, progress) {
        const job = await this.db.table(this.storeName).update(id, {
            progress: Math.min(100, Math.max(0, progress)),
        });
        this._emit("job:progress", job);
        return job;
    }

    /**
     * Obtém todos os jobs
     */
    async all() {
        return this.db.table(this.storeName).orderBy("createdAt", "desc").all();
    }

    /**
     * Obtém jobs pendentes
     */
    async pending() {
        return this.db
            .table(this.storeName)
            .where("status", JobStatus.PENDING)
            .orderBy("priority", "desc")
            .all();
    }

    /**
     * Obtém jobs em processamento
     */
    async processing() {
        return this.db
            .table(this.storeName)
            .where("status", JobStatus.PROCESSING)
            .all();
    }

    /**
     * Obtém jobs completados
     */
    async completed() {
        return this.db
            .table(this.storeName)
            .where("status", JobStatus.COMPLETED)
            .orderBy("updatedAt", "desc")
            .all();
    }

    /**
     * Obtém jobs com falha
     */
    async failed() {
        return this.db
            .table(this.storeName)
            .where("status", JobStatus.FAILED)
            .all();
    }

    /**
     * Obtém um job por ID
     * @param {number} id - ID do job
     */
    async get(id) {
        return this.db.table(this.storeName).find(id);
    }

    /**
     * Conta jobs por status
     */
    async count(status = null) {
        if (status) {
            return this.db
                .table(this.storeName)
                .where("status", status)
                .count();
        }
        return this.db.table(this.storeName).count();
    }

    /**
     * Obtém estatísticas da fila
     */
    async stats() {
        const all = await this.all();

        return {
            total: all.length,
            pending: all.filter((j) => j.status === JobStatus.PENDING).length,
            processing: all.filter((j) => j.status === JobStatus.PROCESSING)
                .length,
            completed: all.filter((j) => j.status === JobStatus.COMPLETED)
                .length,
            failed: all.filter((j) => j.status === JobStatus.FAILED).length,
            cancelled: all.filter((j) => j.status === JobStatus.CANCELLED)
                .length,
        };
    }

    /**
     * Remove um job
     * @param {number} id - ID do job
     */
    async remove(id) {
        const job = await this.get(id);
        if (!job) return false;

        await this.db.table(this.storeName).delete(id);
        this._emit("job:removed", job);
        console.log(`📋 Job removido: #${id}`);
        return true;
    }

    /**
     * Marca job como completado
     * @param {number} id - ID do job
     * @param {any} result - Resultado (opcional)
     * @param {boolean} remove - Remove após completar
     */
    async complete(id, result = null, remove = false) {
        const job = await this.db.table(this.storeName).update(id, {
            status: JobStatus.COMPLETED,
            result,
            completedAt: new Date().toISOString(),
        });

        this._emit("job:completed", job);
        console.log(`✅ Job completado: #${id}`);

        if (remove) {
            await this.remove(id);
        }

        return job;
    }

    /**
     * Marca job como falha
     * @param {number} id - ID do job
     * @param {string} error - Mensagem de erro
     */
    async fail(id, error) {
        const job = await this.get(id);
        if (!job) return null;

        const updates = {
            error,
            attempts: job.attempts + 1,
        };

        // Se ainda tem tentativas, volta para pending
        if (job.attempts + 1 < job.maxAttempts) {
            updates.status = JobStatus.PENDING;
            updates.scheduledAt = new Date(
                Date.now() + this.retryDelay * (job.attempts + 1)
            ).toISOString();
        } else {
            updates.status = JobStatus.FAILED;
            updates.failedAt = new Date().toISOString();
        }

        const updated = await this.db.table(this.storeName).update(id, updates);

        if (updated.status === JobStatus.FAILED) {
            this._emit("job:failed", updated);
            console.error(`❌ Job falhou: #${id} - ${error}`);
        } else {
            this._emit("job:retry", updated);
            console.warn(
                `🔄 Job será reenviado: #${id} (tentativa ${updated.attempts}/${job.maxAttempts})`
            );
        }

        return updated;
    }

    /**
     * Cancela um job
     * @param {number} id - ID do job
     */
    async cancel(id) {
        const job = await this.db.table(this.storeName).update(id, {
            status: JobStatus.CANCELLED,
            cancelledAt: new Date().toISOString(),
        });

        this._emit("job:cancelled", job);
        console.log(`🚫 Job cancelado: #${id}`);
        return job;
    }

    /**
     * Reenvia um job falho
     * @param {number} id - ID do job
     */
    async retry(id) {
        const job = await this.db.table(this.storeName).update(id, {
            status: JobStatus.PENDING,
            attempts: 0,
            error: null,
            scheduledAt: new Date().toISOString(),
        });

        this._emit("job:retry", job);
        console.log(`🔄 Job reagendado: #${id}`);

        // Processa imediatamente se online
        if (navigator.onLine) {
            this.processNext();
        }

        return job;
    }

    /**
     * Reenvia todos os jobs falhos
     */
    async retryAll() {
        const failed = await this.failed();
        for (const job of failed) {
            await this.retry(job.id);
        }
        return failed.length;
    }

    /**
     * Força a sincronização da fila
     */
    async sync() {
        console.log("📋 Sincronização manual disparada");
        return this.processAll();
    }

    /**
     * Processa próximo job da fila
     */
    async processNext() {
        if (this.isProcessing || !navigator.onLine) return;

        const pending = await this.db
            .table(this.storeName)
            .where(
                (job) =>
                    job.status === JobStatus.PENDING &&
                    new Date(job.scheduledAt) <= new Date()
            )
            .orderBy("priority", "desc")
            .first();

        if (!pending) return;

        await this._processJob(pending);

        // Processa próximo com um pequeno delay para não travar a UI
        setTimeout(() => this.processNext(), 100);
    }

    /**
     * Processa todos os jobs pendentes
     */
    async processAll() {
        if (!navigator.onLine) {
            console.warn(
                "📋 Sem conexão. Jobs serão processados quando online."
            );
            return;
        }

        console.log("📋 Processando todos os jobs pendentes...");
        const pending = await this.pending();

        for (const job of pending) {
            await this._processJob(job);
        }

        console.log(`📋 ${pending.length} jobs processados`);
        return pending.length;
    }

    /**
     * Processa um job específico
     * @param {string} type - Tipo do job (processa todos deste tipo)
     */
    async process(type) {
        const jobs = await this.db
            .table(this.storeName)
            .where(
                (job) => job.type === type && job.status === JobStatus.PENDING
            )
            .all();

        for (const job of jobs) {
            await this._processJob(job);
        }

        return jobs.length;
    }

    /**
     * Processa um job
     */
    async _processJob(job) {
        const handler = this.handlers.get(job.type);

        if (!handler) {
            console.error(`❌ Ação não encontrada: ${job.type}`);
            await this.fail(job.id, "Ação não encontrada");
            return;
        }

        // Marca como processando
        await this.db.table(this.storeName).update(job.id, {
            status: JobStatus.PROCESSING,
            startedAt: new Date().toISOString(),
        });

        this.isProcessing = true;
        this._emit("job:processing", job);

        try {
            let result;

            // Suporte a timeout
            if (job.timeout > 0) {
                const timeoutPromise = new Promise((_, reject) =>
                    setTimeout(
                        () => reject(new Error("Job Timeout")),
                        job.timeout
                    )
                );
                result = await Promise.race([
                    handler(job.data, job),
                    timeoutPromise,
                ]);
            } else {
                result = await handler(job.data, job);
            }

            // Se deleteOnComplete for true, remove o job após sucesso
            if (job.deleteOnComplete) {
                await this.complete(job.id, result, true);
            } else {
                await this.complete(job.id, result, false);
            }
        } catch (error) {
            await this.fail(job.id, error.message || String(error));
        } finally {
            this.isProcessing = false;
        }
    }

    /**
     * Limpa jobs completados
     * @param {number} olderThanMs - Remove mais antigos que X ms
     */
    async clearCompleted(olderThanMs = 0) {
        const completed = await this.completed();
        const cutoff = Date.now() - olderThanMs;
        let count = 0;

        for (const job of completed) {
            const completedAt = new Date(
                job.completedAt || job.updatedAt
            ).getTime();
            if (completedAt < cutoff) {
                await this.remove(job.id);
                count++;
            }
        }

        console.log(`📋 ${count} jobs completados removidos`);
        return count;
    }

    /**
     * Limpa todos os jobs
     */
    async clear() {
        await this.db.table(this.storeName).truncate();
        console.log("📋 Fila limpa");
        this._emit("queue:cleared");
    }

    /**
     * Adiciona listener de eventos
     * @param {string} event - Nome do evento
     * @param {Function} callback - Callback
     */
    on(event, callback) {
        if (!this._listeners.has(event)) {
            this._listeners.set(event, new Set());
        }
        this._listeners.get(event).add(callback);
        return () => this.off(event, callback);
    }

    /**
     * Remove listener
     * @param {string} event - Nome do evento
     * @param {Function} callback - Callback
     */
    off(event, callback) {
        const listeners = this._listeners.get(event);
        if (listeners) {
            listeners.delete(callback);
        }
    }

    /**
     * Emite evento
     */
    _emit(event, data) {
        const listeners = this._listeners.get(event);
        if (listeners) {
            listeners.forEach((cb) => {
                try {
                    cb(data);
                } catch (e) {
                    console.error("Erro no listener:", e);
                }
            });
        }

        // Também emite como CustomEvent no document
        document.dispatchEvent(
            new CustomEvent(`spa:${event}`, { detail: data })
        );
    }
}

// Export JobQueue
export { JobQueue };

// Compatibilidade com uso global (opcional)
if (typeof window !== "undefined") {
    window.JobStatus = JobStatus;
    window.JobQueue = JobQueue;
}
