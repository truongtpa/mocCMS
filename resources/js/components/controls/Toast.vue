<template>
  <div class="custom-toast-container">
    <transition-group name="toast-list">
      <div
        v-for="item in toastState.toasts"
        :key="item.id"
        :class="['custom-toast', `toast-${item.type}`]"
        role="alert"
        @mouseenter="pauseToast(item.id)"
        @mouseleave="resumeToast(item.id)"
      >
        <div class="toast-indicator"></div>
        <div class="toast-icon">
          <i :class="getIcon(item.type)"></i>
        </div>
        <div class="toast-body" v-html="item.message"></div>
        <button class="toast-close-btn" @click="removeToast(item.id)">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { toastState, toast } from '@/store/toast'

const removeToast = (id) => {
  toast.remove(id)
}

const pauseToast = (id) => {
  toast.pause(id)
}

const resumeToast = (id) => {
  toast.resume(id)
}

const getIcon = (type) => {
  switch (type) {
    case 'success':
      return 'fas fa-check-circle'
    case 'error':
      return 'fas fa-exclamation-circle'
    case 'warning':
      return 'fas fa-exclamation-triangle'
    case 'info':
    default:
      return 'fas fa-info-circle'
  }
}
</script>

<style scoped>
.custom-toast-container {
  position: fixed;
  top: 72px;
  right: 20px;
  z-index: 999999;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 10px;
  width: auto;
  max-width: calc(100vw - 32px);
  pointer-events: none;
}

.custom-toast {
  pointer-events: auto !important;
  display: flex;
  align-items: center;
  padding: 12px 16px;
  border-radius: 8px;
  background: #ffffff;
  color: #1e293b;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  font-family: inherit;
  font-size: 13px;
  position: relative;
  overflow: hidden;
  transition: all 0.25s cubic-bezier(0.215, 0.61, 0.355, 1);
  min-width: 280px;
  max-width: 480px;
  width: max-content;
}

.toast-indicator {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
}

.toast-success .toast-indicator { background: #10b981; }
.toast-error .toast-indicator { background: #ef4444; }
.toast-warning .toast-indicator { background: #f59e0b; }
.toast-info .toast-indicator { background: #3b82f6; }

.toast-icon {
  flex-shrink: 0;
  margin-left: 2px;
  margin-right: 10px;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.toast-success .toast-icon { color: #10b981; }
.toast-error .toast-icon { color: #ef4444; }
.toast-warning .toast-icon { color: #f59e0b; }
.toast-info .toast-icon { color: #3b82f6; }

.toast-body {
  flex-grow: 1;
  font-weight: 600;
  line-height: 1.4;
  padding-right: 8px;
  word-break: break-word;
  color: #334155;
}

:deep(.toast-body br) {
  margin-bottom: 4px;
}

.toast-close-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
  font-size: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.15s ease;
  flex-shrink: 0;
  margin-left: 6px;
}

.toast-close-btn:hover {
  color: #1e293b;
}

/* Vue Transitions */
.toast-list-enter-active,
.toast-list-leave-active {
  transition: all 0.25s cubic-bezier(0.215, 0.61, 0.355, 1);
}

.toast-list-enter-from {
  opacity: 0;
  transform: translateX(40px) scale(0.95);
}

.toast-list-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

.toast-list-leave-active {
  position: absolute;
  width: 100%;
}
</style>
