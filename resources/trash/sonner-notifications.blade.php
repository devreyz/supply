<div class="sonner-container fixed top-20 right-4 z-[9999] w-[380px] space-y-1"
    x-data="sonnerNotifications()"
    
    @keydown.escape="closeAll">

    <template x-for="(notification, index) in notifications" :key="notification.id">
        <div class="relative"
            x-show="notification.visible"
            x-transition:enter="transform transition-all duration-300 ease-out"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition-all duration-300 ease-in"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            @touchstart="startSwipe($event, notification.id)"
            @touchmove="swipeMove($event, notification.id)"
            @touchend="endSwipe(notification.id)">

            <div class="group bg-card backdrop-blur-lg rounded-lg shadow-xl border border-border p-2 pr-8 relative overflow-hidden"
                :class="{
                     '!bg-success/10 !border-success': notification.type === 'success',
                     '!bg-error/10 !border-error': notification.type === 'error',
                     '!bg-warning/10 !border-warning': notification.type === 'warning',
                     '!bg-info/10 !border-info': notification.type === 'info'
                 }">

                <!-- Ícone de status -->
                <div class="absolute left-4 top-4">
                    <template x-if="notification.type === 'success'">
                        <svg class="w-6 h-6 text-green-600">
                            <use href="#icon-check-circle" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'info'">
                        <svg class="w-6 h-6 text-destructive">
                            <use href="#icon-details" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'warning'">
                        <svg class="w-6 h-6 text-warning">
                            <use href="#icon-warning" />
                        </svg>
                    </template>

                    <!-- Adicione outros tipos -->
                </div>

                <div class="ml-9">
                    <!-- Título e conteúdo -->
                    <h3 class="font-medium text-text mb-1" x-text="notification.title"></h3>
                    <p class="text-sm text-gray-600" x-text="notification.message"></p>

                    <!-- Ações -->
                    <div class="mt-3 flex gap-2" x-show="notification.actions">
                        <template x-for="action in notification.actions">
                            <button @click="handleAction(action, notification.id)"
                                class="text-xs px-3 py-1 rounded-md border border-gray-200 hover:bg-gray-50">
                                <span x-text="action.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Botão de fechar -->
                <button @click="dismiss(notification.id)"
                    class="absolute right-2 top-2 opacity-0 group-hover:opacity-100 transition-opacity p-1 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5">
                        <use href="#icon-x" />
                    </svg>
                </button>

                <!-- Barra de progresso -->
                <div class="absolute bottom-0 left-0 h-0.5 bg-primary w-full"
                    x-show="notification.autoClose"
                    x-bind:style="`animation: progress ${notification.duration}ms linear;`"></div>
            </div>
        </div>
    </template>
</div>
