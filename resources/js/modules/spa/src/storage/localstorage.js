/**
 * 💾 SPA Framework - LocalStorage ORM
 * Wrapper assíncrono para localStorage com API similar ao IndexedDB ORM
 */

/**
 * ORM para localStorage
 */
export class LocalStorageORM {
    constructor(prefix = "spa_") {
        this.prefix = prefix;
        this._cache = new Map();
        this._watchers = new Map();
    }

    /**
     * Gera chave com prefixo
     */
    _key(key) {
        return `${this.prefix}${key}`;
    }

    /**
     * Obtém um valor
     * @param {string} key - Chave
     * @param {any} defaultValue - Valor padrão
     */
    get(key, defaultValue = null) {
        // Verifica cache primeiro
        if (this._cache.has(key)) {
            return this._cache.get(key);
        }

        try {
            const item = localStorage.getItem(this._key(key));
            if (item === null) return defaultValue;

            const parsed = JSON.parse(item);
            this._cache.set(key, parsed);
            return parsed;
        } catch (e) {
            console.warn(`Erro ao ler localStorage[${key}]:`, e);
            return defaultValue;
        }
    }

    /**
     * Define um valor
     * @param {string} key - Chave
     * @param {any} value - Valor
     */
    set(key, value) {
        try {
            const serialized = JSON.stringify(value);
            localStorage.setItem(this._key(key), serialized);

            // Atualiza cache
            this._cache.set(key, value);

            // Notifica watchers
            this._notify(key, value);

            return true;
        } catch (e) {
            console.error(`Erro ao salvar localStorage[${key}]:`, e);
            return false;
        }
    }

    /**
     * Remove um valor
     * @param {string} key - Chave
     */
    remove(key) {
        localStorage.removeItem(this._key(key));
        this._cache.delete(key);
        this._notify(key, null);
        return true;
    }

    /**
     * Verifica se existe
     * @param {string} key - Chave
     */
    has(key) {
        return localStorage.getItem(this._key(key)) !== null;
    }

    /**
     * Obtém todas as chaves
     */
    keys() {
        const keys = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith(this.prefix)) {
                keys.push(key.substring(this.prefix.length));
            }
        }
        return keys;
    }

    /**
     * Obtém todos os valores
     */
    all() {
        const data = {};
        this.keys().forEach((key) => {
            data[key] = this.get(key);
        });
        return data;
    }

    /**
     * Limpa todos os dados com o prefixo
     */
    clear() {
        this.keys().forEach((key) => {
            this.remove(key);
        });
        this._cache.clear();
        return true;
    }

    /**
     * Obtém ou define valor (getOrSet)
     * @param {string} key - Chave
     * @param {any|Function} defaultValue - Valor padrão ou função que retorna o valor
     */
    async remember(key, defaultValue) {
        if (this.has(key)) {
            return this.get(key);
        }

        const value =
            typeof defaultValue === "function"
                ? await defaultValue()
                : defaultValue;

        this.set(key, value);
        return value;
    }

    /**
     * Incrementa um valor numérico
     * @param {string} key - Chave
     * @param {number} amount - Quantidade (default: 1)
     */
    increment(key, amount = 1) {
        const current = this.get(key, 0);
        const newValue = (Number(current) || 0) + amount;
        this.set(key, newValue);
        return newValue;
    }

    /**
     * Decrementa um valor numérico
     * @param {string} key - Chave
     * @param {number} amount - Quantidade (default: 1)
     */
    decrement(key, amount = 1) {
        return this.increment(key, -amount);
    }

    /**
     * Adiciona item a um array
     * @param {string} key - Chave
     * @param {any} value - Valor a adicionar
     */
    push(key, value) {
        const arr = this.get(key, []);
        if (!Array.isArray(arr)) {
            throw new Error(`${key} não é um array`);
        }
        arr.push(value);
        this.set(key, arr);
        return arr;
    }

    /**
     * Remove e retorna último item de um array
     * @param {string} key - Chave
     */
    pop(key) {
        const arr = this.get(key, []);
        if (!Array.isArray(arr)) {
            throw new Error(`${key} não é um array`);
        }
        const value = arr.pop();
        this.set(key, arr);
        return value;
    }

    /**
     * Define valor em objeto aninhado
     * @param {string} key - Chave principal
     * @param {string} path - Caminho (ex: 'user.settings.theme')
     * @param {any} value - Valor
     */
    setNested(key, path, value) {
        const obj = this.get(key, {});
        const keys = path.split(".");
        let current = obj;

        for (let i = 0; i < keys.length - 1; i++) {
            if (!(keys[i] in current)) {
                current[keys[i]] = {};
            }
            current = current[keys[i]];
        }

        current[keys[keys.length - 1]] = value;
        this.set(key, obj);
        return obj;
    }

    /**
     * Obtém valor de objeto aninhado
     * @param {string} key - Chave principal
     * @param {string} path - Caminho (ex: 'user.settings.theme')
     * @param {any} defaultValue - Valor padrão
     */
    getNested(key, path, defaultValue = null) {
        const obj = this.get(key, {});
        const keys = path.split(".");
        let current = obj;

        for (const k of keys) {
            if (current && typeof current === "object" && k in current) {
                current = current[k];
            } else {
                return defaultValue;
            }
        }

        return current;
    }

    /**
     * Observa mudanças em uma chave
     * @param {string} key - Chave
     * @param {Function} callback - Callback (newValue, oldValue)
     * @returns {Function} Função para cancelar observação
     */
    watch(key, callback) {
        if (!this._watchers.has(key)) {
            this._watchers.set(key, new Set());
        }
        this._watchers.get(key).add(callback);

        // Retorna função para cancelar
        return () => {
            const watchers = this._watchers.get(key);
            if (watchers) {
                watchers.delete(callback);
            }
        };
    }

    /**
     * Notifica watchers sobre mudança
     */
    _notify(key, newValue) {
        const watchers = this._watchers.get(key);
        if (watchers) {
            const oldValue = this._cache.get(key);
            watchers.forEach((callback) => {
                try {
                    callback(newValue, oldValue);
                } catch (e) {
                    console.error("Erro no watcher:", e);
                }
            });
        }
    }

    /**
     * Define valor com expiração
     * @param {string} key - Chave
     * @param {any} value - Valor
     * @param {number} ttl - Tempo em segundos
     */
    setWithExpiry(key, value, ttl) {
        const item = {
            value,
            expiry: Date.now() + ttl * 1000,
        };
        return this.set(key, item);
    }

    /**
     * Obtém valor com expiração
     * @param {string} key - Chave
     * @param {any} defaultValue - Valor padrão
     */
    getWithExpiry(key, defaultValue = null) {
        const item = this.get(key);
        if (!item) return defaultValue;

        if (item.expiry && Date.now() > item.expiry) {
            this.remove(key);
            return defaultValue;
        }

        return item.value;
    }

    /**
     * Exporta todos os dados
     */
    export() {
        return JSON.stringify(this.all(), null, 2);
    }

    /**
     * Importa dados
     * @param {string} json - JSON com dados
     */
    import(json) {
        const data = JSON.parse(json);
        Object.entries(data).forEach(([key, value]) => {
            this.set(key, value);
        });
        return true;
    }

    /**
     * Obtém tamanho usado (aproximado)
     */
    size() {
        let total = 0;
        this.keys().forEach((key) => {
            const item = localStorage.getItem(this._key(key));
            if (item) {
                total += item.length * 2; // UTF-16 = 2 bytes por char
            }
        });
        return total;
    }

    /**
     * Verifica se está próximo do limite
     * @param {number} threshold - Porcentagem (0-1)
     */
    isNearLimit(threshold = 0.9) {
        const used = this.size();
        const limit = 5 * 1024 * 1024; // 5MB (limite típico)
        return used >= limit * threshold;
    }
}

/**
 * Instância singleton para uso fácil
 */
export const Storage = new LocalStorageORM("spa_");

// Compatibilidade com uso global (opcional)
if (typeof window !== "undefined") {
    window.LocalStorageORM = LocalStorageORM;
    window.Storage = Storage;
}
