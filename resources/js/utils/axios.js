import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
});
window.axios = api;

/**
 * Bắt lỗi HTTP tập trung: mọi request hỏng đều hiện đúng message + errors server trả về.
 * Nhờ vậy từng lời gọi không cần .catch() chỉ để toast một chuỗi chung chung nữa.
 * Vẫn reject lại để nơi gọi tự xử tiếp nếu cần (rollback, đóng modal...).
 */
function bapLoi(loi) {
    const duLieu = loi?.response?.data;

    if (duLieu && typeof duLieu === 'object' && ('message' in duLieu || 'errors' in duLieu)) {
        window.func.toastError(duLieu);
    } else if (loi?.response) {
        window.func.toastError('Máy chủ trả về lỗi ' + loi.response.status, loi.response.statusText || '');
    } else if (loi?.request) {
        window.func.toastError('Không kết nối được máy chủ', 'Kiểm tra đường truyền rồi thử lại.');
    } else {
        window.func.toastError('Có lỗi xảy ra', loi?.message || String(loi ?? ''));
    }

    return Promise.reject(loi);
}

api.interceptors.response.use(r => r, bapLoi);
axios.interceptors.response.use(r => r, bapLoi);

export default api;
