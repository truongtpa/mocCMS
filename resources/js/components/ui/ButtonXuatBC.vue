<template>
    <DropDown
        v-if="ds_bieu_mau.length > 0"
        :text="title"
        icon="fas fa-file-download"
        :buttonClass="`btn-primary ${buttonClass}`"
        :items="ds_bieu_mau"
        :disabled="disabled"
    />
    <Teleport to="body">
        <ModalXuatBaoCao ref="mdXuatBC" />
    </Teleport>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import ModalXuatBaoCao from './ModalXuatBaoCao.vue'
import DropDown from '../controls/DropDown.vue'

const props = defineProps({
    chuyen_muc: {
        type: [String],
        required: true,
        default: '',
    },
    title: { type: String, default: 'Xuất biểu mẫu' },
    disabled: { type: Boolean, default: false },
    buttonClass: { type: String, default: 'btn-primary' },
})

const API_DS_MAU_BC = 'BaoCaoWordController.getBCByChuyenMuc'

const ds_bieu_mau = ref([])

const mdXuatBC = ref(null)
const thucThiBaoCao = async (item) => {
    mdXuatBC.value?.execute(item)
    return
}
const loadBaoCao = () => {
    axios
        .get(route(API_DS_MAU_BC), {
            params: {
                chuyen_muc: props.chuyen_muc,
            },
            showLoading: false,
        })
        .then((response) => {
            const status = response.data.status
            if (status == 200) {
                const data = response.data.data
                ds_bieu_mau.value = data?.reduce((acc, item, index) => {
                    acc.push({
                        label: item.tieu_de,
                        value: item.id_mau_bao_cao,
                        icon: item.icon ?? 'fas fa-download',
                        action: () => thucThiBaoCao(item),
                    })
                    if (index < data.length - 1) {
                        acc.push({
                            divider: true,
                        })
                    }

                    return acc
                }, [])
            }
        })
}

onMounted(() => {
    loadBaoCao()
})
</script>
