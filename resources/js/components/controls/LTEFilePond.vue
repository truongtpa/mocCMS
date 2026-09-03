<template>
  <div :class="[hasLabel ? 'form-group mb-2' : 'm-0 p-0', extraClasses]">
    <label v-if="hasLabel" :class="labelClass">{{ label }}</label>

    <FilePond
      ref="pond"
      :name="name"
      :label-idle="labelIdle"
      :allow-multiple="allowMultiple"
      :max-files="maxFiles"
      :max-file-size="maxFileSize"
      :accepted-file-types="acceptedFileTypes"
      :disabled="isDisabled"
      :server="serverOptions"
      :instant-upload="false"
      @addfile="onAddFile"
      @removefile="onRemoveFile"
    />
  </div>
</template>

<script>
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'

const FilePond = vueFilePond(
  FilePondPluginFileValidateSize,
  FilePondPluginFileValidateType,
  FilePondPluginImagePreview
)

export default {
  name: 'LTEFilePond',
  components: {
    FilePond
  },
  props: {
    label: {
      type: String,
      default: ''
    },
    labelClass: {
      type: String,
      default: 'font-weight-bold small text-muted text-xs mb-1'
    },
    name: {
      type: String,
      default: 'file'
    },
    labelIdle: {
      type: String,
      default: 'Kéo & Thả tệp vào đây hoặc <span class="filepond--label-action">Chọn Tệp</span>'
    },
    allowMultiple: {
      type: Boolean,
      default: false
    },
    maxFiles: {
      type: Number,
      default: 5
    },
    maxFileSize: {
      type: String,
      default: '10MB'
    },
    acceptedFileTypes: {
      type: [Array, String],
      default: () => []
    },
    isDisabled: {
      type: Boolean,
      default: false
    },
    folder: {
      type: String,
      default: 'uploads'
    },
    modelValue: {
      type: [String, Array, Object, File],
      default: null
    },
    class: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue', 'change', 'remove'],
  data() {
    return {
      isUpdatingFromPond: false
    }
  },
  computed: {
    hasLabel() {
      return this.label && String(this.label).trim() !== ''
    },
    extraClasses() {
      return this.class || ''
    },
    serverOptions() {
      const self = this
      return {
        load: (source, load, error) => {
          if (!source) {
            error('Đường dẫn không hợp lệ')
            return
          }
          if (typeof source !== 'string') {
            load(source)
            return
          }
          if (source.startsWith('http://') || source.startsWith('https://') || source.startsWith('data:') || source.startsWith('/storage/')) {
            fetch(source)
              .then(res => res.blob())
              .then(blob => load(blob))
              .catch(() => error('Không thể tải file xem trước'))
          } else {
            const url = window.route ? window.route('FileController.getFileUrl', { path: source }) : `/admin/file/url?path=${encodeURIComponent(source)}`
            self.$axios.get(url).then(res => {
              if (res.data && res.data.status === 200 && res.data.data.url) {
                fetch(res.data.data.url)
                  .then(r => r.blob())
                  .then(blob => load(blob))
                  .catch(() => error('Lỗi khi tải ảnh từ S3'))
              } else {
                error('Không lấy được URL file')
              }
            }).catch(() => error('Không tìm thấy tệp'))
          }
        }
      }
    }
  },
  watch: {
    modelValue: {
      handler(newVal) {
        if (this.isUpdatingFromPond) return
        this.$nextTick(() => {
          this.updatePondFiles(newVal)
        })
      },
      immediate: true
    }
  },
  methods: {
    updatePondFiles(val) {
      if (!this.$refs.pond) return
      const pond = this.$refs.pond
      if (!val) {
        pond.removeFiles()
        return
      }

      const currentFiles = pond.getFiles()
      if (currentFiles.length > 0) {
        const first = currentFiles[0]
        if (first.file === val || first.source === val || (typeof val === 'string' && first.source === val)) {
          return
        }
      }

      pond.removeFiles()
      if (typeof val === 'string' && val.trim() !== '') {
        pond.addFile(val, { type: 'local' })
      } else if (val instanceof File) {
        pond.addFile(val)
      } else if (Array.isArray(val)) {
        val.forEach(item => {
          if (typeof item === 'string' && item.trim() !== '') pond.addFile(item, { type: 'local' })
          else if (item instanceof File) pond.addFile(item)
        })
      }
    },
    onAddFile(error, fileItem) {
      if (error || !fileItem) return

      // Do NOT overwrite modelValue if file was loaded from server (origin 3 = LOCAL)
      if (fileItem.origin === 3 || fileItem.origin === 'local') {
        return
      }

      const rawFile = fileItem.file
      if (!rawFile) return

      this.isUpdatingFromPond = true
      if (this.allowMultiple) {
        let current = Array.isArray(this.modelValue) ? [...this.modelValue] : []
        if (!current.includes(rawFile)) {
          current.push(rawFile)
        }
        this.$emit('update:modelValue', current)
        this.$emit('change', current)
      } else {
        this.$emit('update:modelValue', rawFile)
        this.$emit('change', rawFile)
      }
      this.$nextTick(() => {
        this.isUpdatingFromPond = false
      })
    },
    onRemoveFile(error, fileItem) {
      if (error) return
      this.isUpdatingFromPond = true
      if (this.allowMultiple) {
        let current = Array.isArray(this.modelValue) ? [...this.modelValue] : []
        const target = fileItem ? fileItem.file : null
        current = current.filter(item => item !== target && item !== fileItem.source)
        this.$emit('update:modelValue', current)
        this.$emit('change', current)
        this.$emit('remove', fileItem)
      } else {
        this.$emit('update:modelValue', '')
        this.$emit('change', '')
        this.$emit('remove', fileItem)
      }
      this.$nextTick(() => {
        this.isUpdatingFromPond = false
      })
    }
  }
}
</script>

<style>
/* Style FilePond AdminLTE Theme Tuning */
.filepond--root {
  font-family: inherit;
  font-size: 0.75rem;
  margin-bottom: 0;
}
.filepond--panel-root {
  background-color: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 0.375rem;
}
.filepond--drop-label {
  color: #64748b;
  min-height: 4.5em;
}
.filepond--label-action {
  color: #2563eb;
  font-weight: 700;
  text-decoration: underline;
}
.filepond--image-preview-wrapper {
  border-radius: 0.25rem;
}
</style>
