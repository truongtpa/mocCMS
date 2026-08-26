import { defineComponent, h } from 'vue';
import moment from 'moment';
import { useToast } from 'vue-toastification'
import api from "./axios.js";
import axios from 'axios';


moment.locale('vi');
const toast = useToast()

function tachNoiDung(a, b) {
    if (b === undefined && a && typeof a === 'object' && !Array.isArray(a)
        && ('message' in a || 'errors' in a)) {
        return [a.message || '', a.errors || ''];
    }
    return b === undefined ? ['', a] : [a, b];
}

// Trải phẳng nội dung lỗi: chuỗi, mảng, hoặc bag lỗi của validator ({field: ['...']}).
function phangNoiDung(noiDung) {
    if (noiDung === null || noiDung === undefined || noiDung === '') return [];
    if (Array.isArray(noiDung)) return noiDung.flatMap(phangNoiDung);
    if (typeof noiDung === 'object') return Object.values(noiDung).flatMap(phangNoiDung);
    return [String(noiDung)];
}

const NoiDungToast = defineComponent({
    props: {
        header: { type: String, default: '' },
        noiDung: { type: String, default: '' }
    },
    render() {
        return h('div', [
            h('div', { class: 'toast-tieu-de' }, this.header),
            h('div', { class: 'toast-noi-dung' }, this.noiDung)
        ]);
    }
});

function hienToast(loai, a, b) {
    const [header, noiDung] = tachNoiDung(a, b);
    const ds = phangNoiDung(noiDung);
    if (ds.length === 0) ds.push('');

    ds.forEach((dong) => {
        const noiDungText = dong;

        if (!header) {
            if (noiDungText) toast[loai](noiDungText);
            return;
        }
        if (!noiDungText) {
            toast[loai](header);
            return;
        }
        toast[loai]({
            component: NoiDungToast,
            props: { header: String(header), noiDung: noiDungText }
        });
    });
}

const func = {
    formatDate: function formatDate(dateString, format = 'L') {
        if (!dateString) return '';
        return moment(dateString).format(format);
    },
    fromNow: function fromNow(dateString) {
        if (!dateString) return '';
        return moment(dateString).fromNow();
    },
    toastError(header, message){
        hienToast('error', header, message);
    },
    toastSuccess(header, message){
        hienToast('success', header, message);
    },
    toastWarning(header, message){
        hienToast('warning', header, message);
    },
    toastInfo(header, message){
        hienToast('info', header, message);
    },

    showLoading(){
        return $loading.show({});
    },
    hideLoading(loading){
        loading.hide({});
    },
}

window.func = func;
export default func;
