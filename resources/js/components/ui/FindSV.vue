<template>
    <div class="col-xl-6 col-lg-10 col-12 p-0  d-flex align-items-center gap-1">
        <div class="flex-grow-1 search-group" @click="onOutsideClick">
            <input type="text" class="form-control" v-model="searchQuery" @input="handleFindSV"
                @focusin="() => { searchFocus = true }" @blur="searchFocusOut"
                placeholder="Họ tên, MSSV. Ví dụ: nguyenvanb, 25006868, Nguyễn Văn, nguyenvan, nguyen b,..."></input>
            <div v-if="suggestSVList.length && !useUI.isMobile() && searchFocus" class="suggest-gv">
                <div v-for="item in suggestSVList" @mousedown="onSuggestMouseDown" @click="handleSuggestClick(item)"
                    class="suggest-item d-flex align-items-center justify-content-between text-truncate">
                    <div class=" d-flex align-items-center">
                        <i class="fa-solid fa-magnifying-glass"></i>&nbsp;&nbsp;

                        <div>
                            <span :style="{
                                fontFamily: 'Inter, sans-serif',
                                fontVariantNumeric: 'tabular-nums',
                            }">{{
                                `${item.mssv}`
                            }}</span>
                            <span>{{
                                `&nbsp;-&nbsp;${item.ho_ten}` }}</span>
                        </div>
                    </div>

                    <div>{{ `&nbsp;&nbsp;(${item.ma_lop ?? 'Không rõ'})` }}</div>
                </div>
                <!-- <div class="suggest-item d-flex align-items-center text-truncate">
                                        <i class="fa-solid fa-empty-set"></i>&nbsp;&nbsp;
                                        <div>Không tìm thấy kết quả</div>
                                    </div> -->
            </div>
            <div v-if="suggestSVList.length && useUI.isMobile() && searchFocus" class="suggest-gv">
                <div v-for="item in suggestSVList"
                    class="suggest-item d-flex align-items-center justify-content-between"
                    @mousedown="onSuggestMouseDown" @click="handleSuggestClick(item)">
                    <div class=" d-flex align-items-center ">
                        <i class="fa-solid fa-magnifying-glass"></i>&nbsp;&nbsp;

                        <div>
                            <span :style="{
                                fontFamily: 'Inter, sans-serif',
                                fontVariantNumeric: 'tabular-nums',
                            }">{{
                                `${item.mssv}`
                            }}</span>
                            <span>{{
                                `&nbsp;-&nbsp;${item.ho_ten}` }}</span>
                        </div>
                    </div>

                    <div>{{ `&nbsp;&nbsp;(${item.ma_lop})` }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useUIManager } from "@/store/ui_manager";
import func from "@/utils/func";
import debounce from "lodash.debounce";
import { reactive, ref, onMounted, watch, onActivated, onBeforeMount, nextTick } from "vue";
const props = defineProps({
    action: Function,
});

const useUI = useUIManager();

const searchQuery = ref("");
const suggestSVList = ref([]);
const searchFocus = ref(true);
const isSearchingSVId = ref(null);

const isClickingOnSuggest = ref(false)

const onSuggestMouseDown = () => {
    isClickingOnSuggest.value = true
}
const searchFocusOut = () => {
    setTimeout(() => {
        if (!isClickingOnSuggest.value) {
            searchFocus.value = false
        }
        isClickingOnSuggest.value = false
    }, 0)
}
const handleFindSV = debounce(() => {
    try {
        axios.get(window.route('TimKiemSinhVienController.findSV'), {
            params: {
                keyword: searchQuery.value
            }
        }).then((response) => {
            if (response.data.status == 200) {
                suggestSVList.value = response.data.data;
            } else {
                suggestSVList.value = []
            }
        }).catch((error) => {
            suggestSVList.value = []
        })
    } catch (error) {
        suggestSVList.value = []
    }
}, 400)

const handleSuggestClick = (item) => {
    searchQuery.value = `${item.mssv} - ${item.ho_ten} (${item.ma_lop ?? 'Không rõ'})`;
    isSearchingSVId.value = item.id_sinh_vien;
    props.action(item);
    suggestSVList.value = suggestSVList.value.filter((e) => e.id_sinh_vien === item.id_sinh_vien)
    searchFocus.value = false

    func.writeSession('id_sinh_vien', { value: item.id_sinh_vien, text: `${item.mssv} - ${item.ho_ten} (${item.ma_lop ?? 'Không rõ'})` })
}
</script>

<style>
/**Suggest */
/* .search-group {
    position: relative;
} */

.suggest-gv {
    position: absolute;
    min-height: 40px;
    max-height: 460px;
    top: calc(100% + 4px);
    width: 100%;
    background-color: white;
    z-index: 100;
    border-radius: 8px;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    overflow-y: auto;
    scrollbar-width: thin;
    padding: 10px 0;
}

.suggest-item {
    display: flex;
    align-items: center;
    height: 40px;
    padding: 5px 8px;
    cursor: pointer;
}

.suggest-item:hover {
    background-color: #eee;
}
</style>