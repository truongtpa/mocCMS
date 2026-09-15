/**
 * Hook cho <Transition> để trượt mở/đóng theo chiều cao.
 *
 * height không animate được từ/đến `auto`, nên phải đo chiều cao thật rồi gán
 * số cụ thể trong lúc chạy, xong mới trả về auto.
 *
 *   <Transition v-bind="collapseTransition()">
 *       <div v-show="open">…</div>
 *   </Transition>
 */
export function collapseTransition(duration = 300) {
    const reduced =
        typeof window !== 'undefined' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    const ms = reduced ? 0 : duration

    const lock = (el) => {
        el.style.overflow = 'hidden'
        el.style.transition = `height ${ms}ms ease-in-out`
    }

    const unlock = (el) => {
        el.style.overflow = ''
        el.style.transition = ''
        el.style.height = ''
    }

    return {
        css: false,
        onEnter(el, done) {
            el.style.height = '0px'
            lock(el)
            // Đọc offsetHeight để ép trình duyệt chốt mốc 0px trước khi đổi
            void el.offsetHeight
            el.style.height = `${el.scrollHeight}px`
            setTimeout(() => {
                unlock(el)
                done()
            }, ms)
        },
        onLeave(el, done) {
            el.style.height = `${el.scrollHeight}px`
            lock(el)
            void el.offsetHeight
            el.style.height = '0px'
            setTimeout(() => {
                unlock(el)
                done()
            }, ms)
        },
    }
}
