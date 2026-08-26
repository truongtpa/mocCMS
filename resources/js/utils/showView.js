let danhSach = null;

function nap() {
    if (danhSach !== null) return danhSach;
    try {
        danhSach = JSON.parse(localStorage.getItem('show_views') || '[]');
    } catch (e) {
        danhSach = [];
    }
    if (!Array.isArray(danhSach)) danhSach = [];
    return danhSach;
}

export function showView(id) {
    if (!id) return false;
    return nap().includes(id);
}

export function napLaiShowView() {
    danhSach = null;
    return nap();
}

export default { showView, napLaiShowView };
