<script setup>
import { ref, watch, onMounted, onBeforeUnmount, onBeforeMount } from 'vue';
import * as monaco from 'monaco-editor';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    label: { type: String, default: '' },
    language: { type: String, default: 'sql' },
    theme: { type: String, default: 'vs-light-override' },
    rows: { type: Number, default: 6 },
});

const initialHeight = `${props.rows * 24}px`;
const height = ref(initialHeight);
const onResize = () => {
    if (editorContainer.value?.offsetHeight > 0)
        height.value = editorContainer.value?.offsetHeight + 'px';
};

const isEmpty = ref(!props.modelValue);

const emit = defineEmits(['update:modelValue', 'input', 'change', 'blur']);

const editorContainer = ref(null);
let editorInstance = null;


const focusEditor = () => {
    editorInstance?.focus();
};
watch(() => props.modelValue, (val) => {
    isEmpty.value = !val;
});
watch(height, (val) => {
    if (val == '0px') height.value = `${props.rows * 24}px`;
});

onMounted(() => {
    editorInstance = monaco.editor.create(editorContainer.value, {
        value: props.modelValue,
        language: props.language,
        theme: props.theme,
        fontSize: 15,
        automaticLayout: true,
        minimap: { enabled: false },
        lineNumbers: 'on',
        scrollBeyondLastLine: false,
        lineNumbersMinChars: 2,
        lineDecorationsWidth: 0,
        scrollbar: {
            verticalScrollbarSize: 4,
            horizontalScrollbarSize: 4,
            handleMouseWheel: true,
            alwaysConsumeMouseWheel: false,
            useShadows: false
        },
        renderLineHighlight: false,
        stickyScroll: {
            enabled: false,
        },
        padding: {
            top: 5,
            bottom: 5
        },
        formatOnType: true,
        formatOnPaste: true,
        fixedOverflowWidgets: true
    });



    editorInstance.onDidChangeModelContent(() => {
        const value = editorInstance.getValue();
        emit('update:modelValue', value);
        emit('input', value);
        emit('change', value);
    });

    editorInstance.onDidBlurEditorWidget(() => {
        const value = editorInstance.getValue();
        emit('blur', value);
    })

    editorInstance.onDidBlurEditorText(() => {
        const formatAction = editorInstance.getAction('editor.action.formatDocument');
        if (formatAction) {
            formatAction.run();
        }
    })

    window.addEventListener('mouseup', onResize);
});
onBeforeUnmount(() => {
    window.removeEventListener('mouseup', onResize);
});
watch(() => props.modelValue, (newVal) => {
    if (editorInstance && editorInstance.getValue() !== newVal) {
        editorInstance.setValue(newVal ?? "");
    }
});

onBeforeUnmount(() => {
    if (editorInstance) {
        editorInstance.dispose();
    }
});
</script>

<template>
    <label v-if="label">{{ label }}</label>
    <div ref="editorContainer" class="code-editor resizable" :style="{ height: height }">
        <pre v-if="isEmpty" class="placeholder" @click="focusEditor">{{ placeholder }}</pre>
    </div>
</template>

<style scoped>
.code-editor.resizable {
    width: 100%;
    resize: vertical;
    overflow: hidden;
    min-height: 120px;
    max-height: 800px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.placeholder {
    position: absolute;
    top: -8px;
    left: 22px;
    color: #888;
    pointer-events: none;
    font-style: normal;
    font-size: 16px;
    font-family: 'Source Sans Pro';
    z-index: 1;
    overflow: hidden;
}

.code-editor {
    position: relative;
}

.code-editor :deep(.monaco-editor) {
    border: none !important;
    box-shadow: none !important;
    outline-color: #ccc !important;
    border-radius: 6px;
    border: 1px solid #ccc;
}
</style>
