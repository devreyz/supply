/**
 * 💾 SPA Framework - IndexedDB ORM
 * ORM simples e assíncrono para IndexedDB
 */

/**
 * ORM para IndexedDB com API fluente
 */
export class IndexedDBORM {
    constructor(dbName = "spa_app", version = 1) {
        this.dbName = dbName;
        this.version = version;
        this.db = null;
        this._stores = new Map();
        this._migrations = [];
        this._observers = new Map();
        this._pendingUpgrade = null;
    }

    /**
     * Observa mudanças em uma tabela
     * @param {string} table - Nome da tabela
     * @param {Function} callback - Função chamada em mudanças
     */
    observe(table, callback) {
        if (!this._observers.has(table)) {
            this._observers.set(table, new Set());
        }
        this._observers.get(table).add(callback);
        return () => this._observers.get(table).delete(callback);
    }

    /**
     * Notifica observadores de uma mudança
     * @param {string} table - Nome da tabela
     * @param {string} type - Tipo de mudança (insert, update, delete)
     * @param {any} data - Dados da mudança
     */
    _notify(table, type, data) {
        if (this._observers.has(table)) {
            this._observers.get(table).forEach((callback) => {
                try {
                    callback({ table, type, data, timestamp: new Date() });
                } catch (e) {
                    console.error(`Erro no observer de ${table}:`, e);
                }
            });
        }
    }

    /**
     * Define uma tabela/store
     * @param {string} name - Nome da tabela
     * @param {Object} options - Opções (keyPath, indexes, softDelete, timestamps)
     */
    async defineTable(name, options = {}) {
        this._stores.set(name, {
            keyPath: options.keyPath || "id",
            autoIncrement: options.autoIncrement !== false,
            indexes: options.indexes || [],
            softDelete: options.softDelete || false,
            timestamps: options.timestamps !== false,
        });

        // Se o DB já estiver aberto e a store não existir, tenta upgrade automático
        try {
            if (this.db && !this.db.objectStoreNames.contains(name)) {
                // Se já houver um upgrade em curso, aguarda
                if (this._pendingUpgrade) {
                    await this._pendingUpgrade;
                    // Verifica novamente se a store foi criada pelo upgrade anterior
                    if (this.db.objectStoreNames.contains(name)) return this;
                }

                this._pendingUpgrade = (async () => {
                    this.db.close();
                    this.version = (this.db.version || 1) + 1;
                    await this.init();
                    this._pendingUpgrade = null;
                })();

                await this._pendingUpgrade;
            }
        } catch (err) {
            console.error("Erro ao criar store automaticamente:", err);
        }

        return this;
    }

    /**
     * Adiciona uma migração
     * @param {Function} migration - Função de migração
     */
    migration(migration) {
        this._migrations.push(migration);
        return this;
    }

    /**
     * Inicializa o banco de dados
     */
    async init() {
        return new Promise((resolve, reject) => {
            const tryOpen = () => {
                const request = indexedDB.open(this.dbName, this.version);

                request.onerror = () => {
                    const err = request.error;

                    // Se o erro for VersionError (requested version < existing),
                    // abra sem versão para descobrir a versão atual e tente um upgrade.
                    if (
                        err &&
                        (err.name === "VersionError" ||
                            /less than the existing version/i.test(
                                String(err.message)
                            ))
                    ) {
                        const probe = indexedDB.open(this.dbName);
                        probe.onsuccess = () => {
                            try {
                                const current = probe.result.version || 1;
                                probe.result.close();
                                this.version = current + 1;
                                // Retry opening with bumped version
                                tryOpen();
                            } catch (e) {
                                reject(
                                    new Error(
                                        `Erro ao recuperar versão atual do DB: ${e}`
                                    )
                                );
                            }
                        };
                        probe.onerror = () => {
                            reject(
                                new Error(
                                    `Erro ao recuperar versão atual do DB: ${probe.error}`
                                )
                            );
                        };
                        return;
                    }

                    reject(new Error(`Erro ao abrir IndexedDB: ${err}`));
                };

                request.onsuccess = () => {
                    this.db = request.result;

                    // Valida se todas as tabelas esperadas existem
                    const missing = this.validateSchema();
                    if (missing.length > 0) {
                        console.warn(
                            `⚠️ Tabelas faltando no IndexedDB: ${missing.join(
                                ", "
                            )}`
                        );
                    }

                    console.log(`💾 IndexedDB "${this.dbName}" conectado`);
                    resolve(this);
                };

                request.onupgradeneeded = (event) => {
                    const db = event.target.result;

                    // Cria stores definidas
                    this._stores.forEach((config, name) => {
                        let store;
                        if (!db.objectStoreNames.contains(name)) {
                            store = db.createObjectStore(name, {
                                keyPath: config.keyPath,
                                autoIncrement: config.autoIncrement,
                            });
                            console.log(`📦 Tabela criada: ${name}`);
                        } else {
                            store =
                                event.currentTarget.transaction.objectStore(
                                    name
                                );
                        }

                        // Cria indexes customizados
                        config.indexes.forEach((index) => {
                            const indexName =
                                typeof index === "string" ? index : index.name;
                            const indexKey =
                                typeof index === "string"
                                    ? index
                                    : index.keyPath || index.name;

                            if (!store.indexNames.contains(indexName)) {
                                const indexOptions =
                                    typeof index === "object"
                                        ? {
                                              unique: index.unique || false,
                                              multiEntry:
                                                  index.multiEntry || false,
                                          }
                                        : {};
                                store.createIndex(
                                    indexName,
                                    indexKey,
                                    indexOptions
                                );
                            }
                        });

                        // Indexes automáticos de timestamps
                        if (config.timestamps) {
                            if (!store.indexNames.contains("createdAt"))
                                store.createIndex("createdAt", "createdAt", {
                                    unique: false,
                                });
                            if (!store.indexNames.contains("updatedAt"))
                                store.createIndex("updatedAt", "updatedAt", {
                                    unique: false,
                                });
                        }

                        // Index automático de soft delete
                        if (config.softDelete) {
                            if (!store.indexNames.contains("deletedAt"))
                                store.createIndex("deletedAt", "deletedAt", {
                                    unique: false,
                                });
                        }
                    });

                    // Stores padrão do framework
                    const defaultStores = ["_settings", "_queue", "_cache"];
                    defaultStores.forEach((name) => {
                        if (!db.objectStoreNames.contains(name)) {
                            db.createObjectStore(name, {
                                keyPath: "id",
                                autoIncrement: true,
                            });
                        }
                    });

                    // Executa migrações
                    this._migrations.forEach((migration) => {
                        try {
                            migration(db, event);
                        } catch (e) {
                            console.error("Erro na migração:", e);
                        }
                    });
                };
            };

            tryOpen();
        });
    }

    /**
     * Retorna um QueryBuilder para uma tabela
     * @param {string} name - Nome da tabela
     */
    table(name) {
        const config = this._stores.get(name) || {};
        return new QueryBuilder(this, name, config);
    }

    /**
     * Valida se o schema do banco está completo
     */
    validateSchema() {
        if (!this.db) return [];
        const expected = Array.from(this._stores.keys());
        const existing = Array.from(this.db.objectStoreNames);
        return expected.filter((t) => !existing.includes(t));
    }

    /**
     * Atalho para tabela (alias)
     */
    from(name) {
        return this.table(name);
    }

    /**
     * Executa transação customizada
     * @param {string[]} stores - Stores envolvidas
     * @param {string} mode - 'readonly' ou 'readwrite'
     * @param {Function} callback - Callback com transaction
     */
    async transaction(stores, mode, callback) {
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(stores, mode);

            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
            tx.onabort = () => reject(new Error("Transação abortada"));

            try {
                callback(tx);
            } catch (error) {
                tx.abort();
                reject(error);
            }
        });
    }

    /**
     * Limpa todas as tabelas
     */
    async clear() {
        const stores = Array.from(this.db.objectStoreNames);

        for (const store of stores) {
            await this.table(store).truncate();
        }
    }

    /**
     * Deleta o banco de dados
     */
    async delete() {
        this.db.close();
        return new Promise((resolve, reject) => {
            const request = indexedDB.deleteDatabase(this.dbName);
            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Exporta todos os dados
     */
    async export() {
        const data = {};
        const stores = Array.from(this.db.objectStoreNames);

        for (const store of stores) {
            data[store] = await this.table(store).all();
        }

        return JSON.stringify(data, null, 2);
    }

    /**
     * Importa dados
     * @param {string} json - JSON com dados
     */
    async import(json) {
        const data = JSON.parse(json);

        for (const [store, records] of Object.entries(data)) {
            if (this.db.objectStoreNames.contains(store)) {
                await this.table(store).truncate();
                for (const record of records) {
                    await this.table(store).insert(record);
                }
            }
        }
    }
}

/**
 * Query Builder para operações na tabela
 */
class QueryBuilder {
    constructor(orm, storeName, config = {}) {
        this.orm = orm;
        this.db = orm.db;
        this.storeName = storeName;
        this.config = config;
        this._where = null;
        this._orderBy = null;
        this._orderDir = "asc";
        this._limit = null;
        this._offset = 0;
        this._withTrashed = false;
        this._onlyTrashed = false;
    }

    /**
     * Inclui registros deletados (soft delete)
     */
    withTrashed() {
        this._withTrashed = true;
        return this;
    }

    /**
     * Retorna apenas registros deletados (soft delete)
     */
    onlyTrashed() {
        this._onlyTrashed = true;
        this._withTrashed = true;
        return this;
    }

    /**
     * Adiciona condição WHERE
     * @param {string|Function} key - Chave ou função de filtro
     * @param {any} value - Valor (opcional)
     */
    where(key, value) {
        if (typeof key === "function") {
            this._where = key;
        } else {
            this._where = (item) => item[key] === value;
        }
        return this;
    }

    /**
     * Adiciona condição WHERE IN
     * @param {string} key - Chave
     * @param {any[]} values - Array de valores
     */
    whereIn(key, values) {
        this._where = (item) => values.includes(item[key]);
        return this;
    }

    /**
     * Adiciona condição WHERE NOT
     * @param {string} key - Chave
     * @param {any} value - Valor
     */
    whereNot(key, value) {
        this._where = (item) => item[key] !== value;
        return this;
    }

    /**
     * Adiciona condição WHERE BETWEEN
     * @param {string} key - Chave
     * @param {any} min - Valor mínimo
     * @param {any} max - Valor máximo
     */
    whereBetween(key, min, max) {
        this._where = (item) => item[key] >= min && item[key] <= max;
        return this;
    }

    /**
     * Adiciona condição WHERE NULL
     * @param {string} key - Chave
     */
    whereNull(key) {
        this._where = (item) => item[key] === null || item[key] === undefined;
        return this;
    }

    /**
     * Adiciona condição WHERE NOT NULL
     * @param {string} key - Chave
     */
    whereNotNull(key) {
        this._where = (item) => item[key] !== null && item[key] !== undefined;
        return this;
    }

    /**
     * Ordena pelo mais recente
     * @param {string} column - Coluna de data
     */
    latest(column = "createdAt") {
        return this.orderBy(column, "desc");
    }

    /**
     * Ordena pelo mais antigo
     * @param {string} column - Coluna de data
     */
    oldest(column = "createdAt") {
        return this.orderBy(column, "asc");
    }

    /**
     * Busca textual em múltiplos campos
     * @param {string} query - Termo de busca
     * @param {string[]} fields - Campos para buscar
     */
    async search(query, fields = []) {
        const all = await this.all();
        const lowerQuery = query.toLowerCase();

        return all.filter((item) => {
            return fields.some((field) => {
                const value = String(item[field] || "").toLowerCase();
                return value.includes(lowerQuery);
            });
        });
    }

    /**
     * Extrai apenas uma coluna dos resultados
     * @param {string} column - Nome da coluna
     */
    async pluck(column) {
        const results = await this.all();
        return results.map((item) => item[column]);
    }

    /**
     * Retorna o último registro
     */
    async last() {
        const results = await this.all();
        return results[results.length - 1] || null;
    }

    /**
     * Retorna um registro aleatório
     */
    async random() {
        const results = await this.all();
        if (results.length === 0) return null;
        return results[Math.floor(Math.random() * results.length)];
    }

    /**
     * Soma os valores de uma coluna
     * @param {string} column - Nome da coluna
     */
    async sum(column) {
        const results = await this.all();
        return results.reduce(
            (acc, item) => acc + (Number(item[column]) || 0),
            0
        );
    }

    /**
     * Média dos valores de uma coluna
     * @param {string} column - Nome da coluna
     */
    async avg(column) {
        const results = await this.all();
        if (results.length === 0) return 0;
        return (await this.sum(column)) / results.length;
    }

    /**
     * Valor mínimo de uma coluna
     * @param {string} column - Nome da coluna
     */
    async min(column) {
        const results = await this.all();
        if (results.length === 0) return null;
        return Math.min(...results.map((item) => Number(item[column]) || 0));
    }

    /**
     * Valor máximo de uma coluna
     * @param {string} column - Nome da coluna
     */
    async max(column) {
        const results = await this.all();
        if (results.length === 0) return null;
        return Math.max(...results.map((item) => Number(item[column]) || 0));
    }

    /**
     * Adiciona ordenação
     * @param {string} key - Chave para ordenar
     * @param {string} direction - 'asc' ou 'desc'
     */
    orderBy(key, direction = "asc") {
        this._orderBy = key;
        this._orderDir = direction;
        return this;
    }

    /**
     * Limita resultados
     * @param {number} count - Número máximo
     */
    limit(count) {
        this._limit = count;
        return this;
    }

    /**
     * Define offset
     * @param {number} count - Número de registros para pular
     */
    offset(count) {
        this._offset = count;
        return this;
    }

    /**
     * Busca todos os registros
     */
    async all() {
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(this.storeName, "readonly");
            const store = tx.objectStore(this.storeName);
            const request = store.getAll();

            request.onsuccess = () => {
                let results = request.result || [];

                // Filtro de Soft Delete
                if (this.config.softDelete && !this._withTrashed) {
                    results = results.filter((item) => !item.deletedAt);
                } else if (this.config.softDelete && this._onlyTrashed) {
                    results = results.filter((item) => !!item.deletedAt);
                }

                // Aplica filtro customizado
                if (this._where) {
                    results = results.filter(this._where);
                }

                // Aplica ordenação
                if (this._orderBy) {
                    results.sort((a, b) => {
                        const valA = a[this._orderBy];
                        const valB = b[this._orderBy];

                        if (valA < valB)
                            return this._orderDir === "asc" ? -1 : 1;
                        if (valA > valB)
                            return this._orderDir === "asc" ? 1 : -1;
                        return 0;
                    });
                }

                // Aplica offset e limit
                if (this._offset > 0 || this._limit !== null) {
                    const end =
                        this._limit !== null
                            ? this._offset + this._limit
                            : undefined;
                    results = results.slice(this._offset, end);
                }

                resolve(results);
            };

            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Busca primeiro registro
     */
    async first() {
        this._limit = 1;
        const results = await this.all();
        return results[0] || null;
    }

    /**
     * Busca por ID
     * @param {any} id - ID do registro
     */
    async find(id) {
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(this.storeName, "readonly");
            const store = tx.objectStore(this.storeName);
            const request = store.get(id);

            request.onsuccess = () => {
                const result = request.result || null;

                // Filtro de Soft Delete
                if (
                    result &&
                    this.config.softDelete &&
                    !this._withTrashed &&
                    result.deletedAt
                ) {
                    return resolve(null);
                }

                resolve(result);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Conta registros
     */
    async count() {
        if (this._where) {
            const results = await this.all();
            return results.length;
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(this.storeName, "readonly");
            const store = tx.objectStore(this.storeName);
            const request = store.count();

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Verifica se existe
     * @param {any} id - ID do registro
     */
    async exists(id) {
        const record = await this.find(id);
        return record !== null;
    }

    /**
     * Insere um registro
     * @param {Object} data - Dados do registro
     */
    async insert(data) {
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(this.storeName, "readwrite");
            const store = tx.objectStore(this.storeName);

            // Adiciona timestamps
            const record = {
                ...data,
                createdAt: data.createdAt || new Date().toISOString(),
                updatedAt: new Date().toISOString(),
            };

            const request = store.add(record);

            request.onsuccess = () => {
                record.id = request.result;
                this.orm._notify(this.storeName, "insert", record);
                resolve(record);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Insere múltiplos registros
     * @param {Object[]} records - Array de registros
     */
    async insertMany(records) {
        const results = [];
        for (const record of records) {
            const result = await this.insert(record);
            results.push(result);
        }
        return results;
    }

    /**
     * Atualiza um registro
     * @param {any} id - ID do registro
     * @param {Object} data - Dados para atualizar
     */
    async update(id, data) {
        return new Promise(async (resolve, reject) => {
            // Busca registro existente
            const existing = await this.find(id);
            if (!existing) {
                reject(new Error(`Registro não encontrado: ${id}`));
                return;
            }

            const tx = this.db.transaction(this.storeName, "readwrite");
            const store = tx.objectStore(this.storeName);

            const record = {
                ...existing,
                ...data,
                id, // Garante que o ID não muda
                updatedAt: new Date().toISOString(),
            };

            const request = store.put(record);

            request.onsuccess = () => {
                this.orm._notify(this.storeName, "update", record);
                resolve(record);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Atualiza ou insere
     * @param {Object} data - Dados do registro
     */
    async upsert(data) {
        if (data.id && (await this.exists(data.id))) {
            return this.update(data.id, data);
        }
        return this.insert(data);
    }

    /**
     * Busca o primeiro ou cria se não existir
     * @param {Object} attributes - Atributos para busca
     * @param {Object} values - Valores adicionais para criação
     */
    async firstOrCreate(attributes, values = {}) {
        const existing = await this.where((item) => {
            return Object.entries(attributes).every(
                ([key, val]) => item[key] === val
            );
        }).first();

        if (existing) return existing;
        return this.insert({ ...attributes, ...values });
    }

    /**
     * Atualiza ou cria baseado em atributos de busca
     * @param {Object} attributes - Atributos para busca
     * @param {Object} values - Valores para atualizar ou criar
     */
    async updateOrCreate(attributes, values = {}) {
        const existing = await this.where((item) => {
            return Object.entries(attributes).every(
                ([key, val]) => item[key] === val
            );
        }).first();

        if (existing) {
            return this.update(existing.id, values);
        }
        return this.insert({ ...attributes, ...values });
    }

    /**
     * Processa resultados em pedaços (chunks) para economizar memória
     * @param {number} size - Tamanho do pedaço
     * @param {Function} callback - Função para processar o pedaço
     */
    async chunk(size, callback) {
        let page = 1;
        let hasMore = true;

        while (hasMore) {
            const { data, pagination } = await this.paginate(page, size);
            if (data.length > 0) {
                await callback(data, page);
            }
            hasMore = pagination.hasMore;
            page++;
        }
    }

    /**
     * Deleta um registro (suporta soft delete)
     * @param {any} id - ID do registro
     */
    async delete(id) {
        if (this.config.softDelete) {
            const result = await this.update(id, {
                deletedAt: new Date().toISOString(),
            });
            this.orm._notify(this.storeName, "delete", { id, soft: true });
            return result;
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(this.storeName, "readwrite");
            const store = tx.objectStore(this.storeName);
            const request = store.delete(id);

            request.onsuccess = () => {
                this.orm._notify(this.storeName, "delete", {
                    id,
                    soft: false,
                });
                resolve(true);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Restaura um registro deletado (soft delete)
     * @param {any} id - ID do registro
     */
    async restore(id) {
        if (!this.config.softDelete) return false;
        return this.update(id, { deletedAt: null });
    }

    /**
     * Deleta permanentemente um registro
     * @param {any} id - ID do registro
     */
    async forceDelete(id) {
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(this.storeName, "readwrite");
            const store = tx.objectStore(this.storeName);
            const request = store.delete(id);

            request.onsuccess = () => resolve(true);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Deleta registros por condição
     */
    async deleteWhere() {
        if (!this._where) {
            throw new Error("deleteWhere requer uma condição where()");
        }

        const records = await this.all();
        for (const record of records) {
            await this.delete(record.id);
        }
        return records.length;
    }

    /**
     * Limpa toda a tabela
     */
    async truncate() {
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(this.storeName, "readwrite");
            const store = tx.objectStore(this.storeName);
            const request = store.clear();

            request.onsuccess = () => {
                this.orm._notify(this.storeName, "truncate", null);
                resolve(true);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Busca por index
     * @param {string} indexName - Nome do index
     * @param {any} value - Valor a buscar
     */
    async findByIndex(indexName, value) {
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(this.storeName, "readonly");
            const store = tx.objectStore(this.storeName);
            const index = store.index(indexName);
            const request = index.getAll(value);

            request.onsuccess = () => resolve(request.result || []);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Incrementa um campo
     * @param {any} id - ID do registro
     * @param {string} field - Campo a incrementar
     * @param {number} amount - Quantidade (default: 1)
     */
    async increment(id, field, amount = 1) {
        const record = await this.find(id);
        if (!record) return null;

        record[field] = (record[field] || 0) + amount;
        return this.update(id, record);
    }

    /**
     * Decrementa um campo
     * @param {any} id - ID do registro
     * @param {string} field - Campo a decrementar
     * @param {number} amount - Quantidade (default: 1)
     */
    async decrement(id, field, amount = 1) {
        return this.increment(id, field, -amount);
    }

    /**
     * Paginação
     * @param {number} page - Número da página (1-based)
     * @param {number} perPage - Itens por página
     */
    async paginate(page = 1, perPage = 10) {
        const total = await this.count();
        const totalPages = Math.ceil(total / perPage);

        this._offset = (page - 1) * perPage;
        this._limit = perPage;

        const data = await this.all();

        return {
            data,
            pagination: {
                page,
                perPage,
                total,
                totalPages,
                hasMore: page < totalPages,
            },
        };
    }
}

// Exporta QueryBuilder também
export { QueryBuilder };

// Compatibilidade com uso global (opcional)
if (typeof window !== "undefined") {
    window.IndexedDBORM = IndexedDBORM;
    window.QueryBuilder = QueryBuilder;
}
