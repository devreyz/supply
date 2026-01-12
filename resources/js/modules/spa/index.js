/**
 * 🚀 SPA Framework - ES Module Entry Point
 * Exporta todos os módulos para uso com Vite/ES imports
 *
 * @version 2.0.0
 */

// Storage
export { IndexedDBORM, QueryBuilder } from "./src/storage/indexeddb.js";
export { LocalStorageORM } from "./src/storage/localstorage.js";

// Offline
export { JobQueue, JobStatus } from "./src/offline/queue.js";

// PWA
export { PWAInstaller } from "./src/pwa/install.js";
export { NotificationManager } from "./src/pwa/notifications.js";

// UI
export {
    DrawerManager,
    SheetManager,
    ModalManager,
    Toast,
} from "./src/ui/modals.js";

// Core
export { SPA, DEFAULT_CONFIG } from "./src/core/spa.js";

// Default export - SPA class
export { SPA as default } from "./src/core/spa.js";
