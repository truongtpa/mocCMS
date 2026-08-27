import moment from 'moment'
import CryptoJS from 'crypto-js'
import { toast } from '@/store/toast'
import Swal from 'sweetalert2'

moment.locale('vi')
const func = {
    // Read more documents here: https://momentjs.com/
    formatDate: function formatDate(dateString, format = 'DD/MM/YYYY') {
        if (!dateString) return ''
        return moment(dateString).format(format)
    },
    fromNow: function fromNow(dateString) {
        if (!dateString) return ''
        return moment(dateString).fromNow() // Relative time, like "2 days ago"
    },
    formatDateTime: function formatDateTime(dateString, format = 'DD/MM/YYYY HH:mm') {
        if (!dateString) return ''
        return moment(dateString).format(format)
    },
    formateTime: function formatTime(seconds, format = 'HH:mm:ss') {
        if (isNaN(seconds)) return ''
        const duration = moment.duration(seconds, 'seconds')
        return moment.utc(duration.asMilliseconds()).format(format)
    },

    generateRandomString: function generateRandomString(length) {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
        let result = ''
        const charactersLength = characters.length
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength))
        }
        return result
    },
    uniqueId: function uniqueId() {
        return Math.random().toString(36).substr(2, 9) + Date.now().toString(36);
    },

    // Custom Toast functions
    toastError(text) {
        if (text && text.constructor === Array) {
            for (var i = 0; i < text.length; i++) {
                toast.error(text[i])
            }
        } else {
            toast.error(text)
        }
    },
    toastSuccess(text) {
        toast.success(text)
    },
    toastWarning(text) {
        toast.warning(text)
    },
    toastInfo(text) {
        toast.info(text)
    },

    // Loading
    showLoading(title = 'Đang xử lý...') {
        Swal.fire({
            title: title,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        })
        return {
            hide: () => Swal.close()
        }
    },
    hideLoading(loading) {
        if (loading && typeof loading.hide === 'function') {
            loading.hide()
        } else {
            Swal.close()
        }
    },
    loading(state, title = 'Đang xử lý...') {
        if (state) {
            this.showLoading(title)
        } else {
            this.hideLoading()
        }
    },
    getImageName(name) {
        const words = name.trim().split(/\s+/).slice(-2)
        return words.map((word) => word[0].toUpperCase()).join('')
    },
    toLatin(str) {
        const accentsMap = {
            a: 'áàạảãâấầậẩẫăắằặẳẵ',
            e: 'éèẹẻẽêếềệểễ',
            i: 'íìịỉĩ',
            o: 'óòọỏõôốồộổỗơớờợởỡ',
            u: 'úùụủũưứừựửữ',
            y: 'ýỳỵỷỹ',
            d: 'đ',
        }

        for (let key in accentsMap) {
            const regex = new RegExp('[' + accentsMap[key] + ']', 'g')
            str = str.replace(regex, key)
        }

        str = str.replace(/[\s,-]+/g, '_')

        return str
        // return str.replace(/\s+/g, '_')
    },

    writeSession(key, value) {
        try {
            const data = typeof value === 'string' ? value : JSON.stringify(value)
            sessionStorage.setItem(key, data)
        } catch (e) {
            console.error('Lỗi khi ghi session:', e)
        }
    },
    readSession(key) {
        try {
            const data = sessionStorage.getItem(key)
            try {
                return JSON.parse(data)
            } catch {
                return data
            }
        } catch (e) {
            console.error('Lỗi khi đọc session:', e)
            return null
        }
    },
    encodeWithKey(data, sessionKey) {
        try {
            const key = CryptoJS.SHA256(sessionKey)
            const iv = CryptoJS.lib.WordArray.random(16)
            const encrypted = CryptoJS.AES.encrypt(JSON.stringify(data), key, {
                iv: iv,
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.Pkcs7,
            })

            return iv.toString() + ':' + encrypted.toString()
        } catch (e) {
            console.error('Lỗi mã hóa:', e)
            return null
        }
    },
    hmacEncrypt(data, sessionKey) {
        try {
            const key = CryptoJS.SHA256(sessionKey)
            const message = String(data)
            return CryptoJS.HmacSHA256(message, key).toString()
        } catch (e) {
            console.error('Lỗi mã hóa:', e)
            return null
        }
    },
}

window.func = func
export default func
