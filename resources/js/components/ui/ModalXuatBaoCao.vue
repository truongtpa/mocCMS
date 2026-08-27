<template>
    <LTEModal ref="mdXacNhanBC" @close="cancle">
        <div class="row" style="">
            <template v-for="item in paramBaoCao">
                <div v-if="item.type == 'none'" class="col-12">
                    <div class="form-group">
                        <label>{{ item.label ?? '' }}</label>
                        <input
                            :placeholder="item.label ?? ''"
                            type="text"
                            class="form-control"
                            :value="dataBaoCaoFromSession?.[item.name]?.text ?? 'Không có dữ liệu'"
                            disabled
                        />
                    </div>
                </div>
                <div v-if="item.type == 'input'" class="col-12">
                    <LTEInput
                        v-model="baoCao[item.name]"
                        :label="item.label ?? ''"
                        :placeholder="item.label ?? ''"
                        type="text"
                    />
                </div>
                <div v-else-if="item.type == 'array'" class="col-12">
                    <LTESelect2Option
                        :placeholder="item.label ?? ''"
                        :label="item.label ?? ''"
                        :data="dataBaoCao[item.name]"
                        :multiple="false"
                        :closeOnSelect="true"
                        :enableDataWatch="true"
                        v-model="baoCao[item.name]"
                        :initValue="baoCao[item.name]"
                    >
                    </LTESelect2Option>
                </div>
                <div v-else-if="item.type == 'select'" class="col-12">
                    <LTESelect2Option
                        :placeholder="item.label ?? ''"
                        :label="item.label ?? ''"
                        :data="dataBaoCao[item.name]"
                        :multiple="false"
                        :closeOnSelect="true"
                        :enableDataWatch="true"
                        v-model="baoCao[item.name]"
                        :initValue="baoCao[item.name]"
                    >
                    </LTESelect2Option>
                </div>
            </template>
            <div v-if="currentBaoCao?.cau_truy_van?.ghi_chu" class="col-12 mb-3">
                <div class="alert alert-info py-2 px-3 m-0 text-sm" style="white-space: pre-line;">
                    <i class="fas fa-info-circle mr-1"></i>
                    <b>Ghi chú:</b> {{ currentBaoCao.cau_truy_van.ghi_chu }}
                </div>
            </div>
            <div class="listen-state w-100 col-12">
                <div class="w-100 d-flex align-items-center position-relative">
                    <LTETextArea
                        v-if="id_mau_bao_cao_ket_qua"
                        v-model="currentBaoCaoKetQua.log"
                        class="w-100"
                        :isDisabled="true"
                        :rows="5"
                        label="Log"
                        placeholder="Log báo cáo hiển thị tại đây"
                    />
                    <LTETextArea
                        v-else
                        class="w-100"
                        :isDisabled="true"
                        :rows="5"
                        label="Log"
                        placeholder="Log báo cáo hiển thị tại đây"
                    />
                    <!-- <LTELoading :text="currentBaoCaoKetQua.log" /> -->
                    <div
                        v-if="id_mau_bao_cao_ket_qua"
                        :class="`badge 
                        ${currentBaoCaoKetQua.trang_thai == 'FAILURE' ? 'bg-danger' : 'bg-info'} position-absolute`"
                        style="top: 38px; right: 18px"
                    >
                        {{ currentBaoCaoKetQua.trang_thai }}
                    </div>
                </div>

                <b>Hướng dẫn:</b>
                <ul class="px-4 pt-2">
                    <li>Chọn các tham số cần để xuất báo cáo.</li>
                    <li>Nhấn nút <b>“Xuất báo cáo”</b> và chờ hệ thống tự động tải file về máy.</li>
                    <li>
                        Trường hợp dữ liệu lớn và cần nhiều thời gian xử lý, bạn có thể theo dõi tiến trình trong phần
                        chi tiết của biểu mẫu báo cáo để tải file khi hoàn tất.
                    </li>
                </ul>

                <div v-if="currentBaoCaoKetQua.file">
                    <LTEButton
                        text="Tải file về máy"
                        :action="
                            () => {
                                downloadFile(currentBaoCaoKetQua.file)
                            }
                        "
                        icon="fas fa-download"
                    />
                </div>
            </div>
        </div>
        <template #action>
            <LTEButton
                v-if="id_mau_bao_cao_ket_qua"
                text="Hủy và khởi tạo lại"
                :action="() => execute(currentBaoCao, false)"
                icon="fas fa-times"
                class="btn-danger"
            />
        </template>
    </LTEModal>
</template>
<script setup>
import { onMounted, onUnmounted, reactive, ref } from 'vue'
import LTELoading from '../controls/LTELoading.vue'
import LTETextArea from '../controls/LTETextArea.vue'
import LTEButton from '../controls/LTEButton.vue'
import { WINDOW } from '@sentry/vue'

const isLoading = ref(false)

const paramBaoCao = ref([])
const currentBaoCao = ref({})
const dataBaoCao = ref({})
const dataBaoCaoFromSession = ref({})
const baoCao = reactive({})
const mdXacNhanBC = ref(null)

const id_mau_bao_cao_ket_qua = ref(null)
const id_listen = ref(null)
const currentBaoCaoKetQua = ref({})

const execute = async (item, reset = true) => {
    if (id_listen.value) {
        clearInterval(id_listen.value)
    }
    currentBaoCaoKetQua.value = {}
    isLoading.value = false
    id_mau_bao_cao_ket_qua.value = null

    paramBaoCao.value = item.cau_truy_van.params
    paramBaoCao.value = item.cau_truy_van.params.sort((a, b) => {
        return a.type === 'none' ? -1 : b.type === 'none' ? 1 : 0
    })
    currentBaoCao.value = item
    if (reset) {
        await axios
            .get(route('BaoCaoWordController.getParamsDetail'), {
                params: {
                    id_mau_bao_cao: item.id_mau_bao_cao,
                },
            })
            .then((response) => {
                if (response.data.status) {
                    dataBaoCao.value = response.data.data
                }
            })
    }
    //func.writeSession('id_sinh_vien', { value: 123, text: '21001400 - Nguyễn Văn A' })
    const paramsFromSession = paramBaoCao.value.reduce((acc, p) => {
        if (p.type == 'none') {
            const data = func.readSession(p.name)

            if (data) {
                acc = {
                    [p.name]: data.value,
                    [p.name + '_text']: data.text,
                }

                dataBaoCaoFromSession.value[p.name] = data
            }
        }
        return acc
    }, {})

    mdXacNhanBC.value.$data.title = item.tieu_de
    mdXacNhanBC.value.$data.save = 'Xuất báo cáo'

    const result = await mdXacNhanBC.value.openModal()
    if (!result) return

    var params = baoCao

    await axios
        .get(
            route('BaoCaoWordController.render', {
                ...params,
                ...paramsFromSession,
                id_mau_bao_cao: item.id_mau_bao_cao,
            }),
        )
        .then((response) => {
            if (response.data.status) {
                const data = response.data.data
                id_mau_bao_cao_ket_qua.value = data.id_mau_bao_cao_ket_qua
                isLoading.value = true
                func.toastSuccess(response.data.message)
                mdXacNhanBC.value.$data.save = 'Đang tạo file báo cáo'
            } else {
                func.toastError(response.data.errors)
            }
        })

    id_listen.value = setInterval(() => {
        listenDownloadKetQua()
    }, 2000)
    // mdXacNhanBC.value.closeModal();
}

const downloadFile = (url) => {
    window.open(url, '_blank')
}

const listenDownloadKetQua = async () => {
    await axios
        .get(route('BaoCaoWordController.listenMauBCKetQua', { id_mau_bao_cao_ket_qua: id_mau_bao_cao_ket_qua.value }))
        .then((response) => {
            if (response.data.status) {
                const data = response.data.data
                currentBaoCaoKetQua.value = data.ket_qua
                if (data && data.ket_qua && data.ket_qua.file) {
                    if (id_listen.value) {
                        isLoading.value = false
                        clearInterval(id_listen.value)
                    }

                    const link = document.createElement('a')
                    link.href = data.ket_qua.file
                    link.download = ''
                    document.body.appendChild(link)
                    link.click()
                    document.body.removeChild(link)
                }

                if (data.ket_qua.trang_thai == 'FAILURE') {
                    if (id_listen.value) {
                        isLoading.value = false
                        clearInterval(id_listen.value)
                    }
                }
            } else {
            }
        })
}

const cancle = () => {
    if (id_listen.value) {
        clearInterval(id_listen.value)
    }
}

defineExpose({
    execute,
    cancle,
})

onUnmounted(() => {
    if (id_listen.value) {
        clearInterval(id_listen.value)
    }
})
</script>
<style scoped></style>
