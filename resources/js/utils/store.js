import axios from "axios";
import func from "@/utils/func.js";
export const store = {
    dsLoaiBaoMat: [
        { value: 1, text: 'Công khai'},
        { value: 2, text: 'Không công khai'},
    ],
    dsTrangThaiVanBan: [
        {value: 1, text: 'Chờ phê duyệt', color: 'primary'},
        {value: 2, text: 'Đã phê duyệt', color: 'warning'},
        {value: 3, text: 'Đang thực hiện', color: 'info'},
        {value: 4, text: 'Hoàn tất/Lưu trữ', color: 'secondary'},
        {value: 5, text: 'Quá hạn xử lý', color: 'danger'},
    ],
}

window.store = store;

