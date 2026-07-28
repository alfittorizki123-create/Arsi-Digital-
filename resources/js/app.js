import './bootstrap';
import Alpine from 'alpinejs';

function getToastContainer() {
    let c = document.getElementById('toast-container');
    if (!c) {
        c = document.createElement('div');
        c.id = 'toast-container';
        c.className = 'fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 w-80 max-w-[calc(100vw-2.5rem)] pointer-events-none';
        document.body.appendChild(c);
    }
    return c;
}

const TOAST_ICONS = { success: 'check_circle', error: 'error', warning: 'warning', info: 'info' };
const TOAST_BORDER = { success: 'border-l-green-500', error: 'border-l-red-500', warning: 'border-l-amber-500', info: 'border-l-blue-500' };
const TOAST_ICON_COLOR = { success: 'text-green-500', error: 'text-red-500', warning: 'text-amber-500', info: 'text-blue-500' };

window.showToast = function (type, message, duration = 5000) {
    const container = getToastContainer();
    const t = type in TOAST_ICONS ? type : 'info';

    const el = document.createElement('div');
    el.className = 'pointer-events-auto backdrop-blur-lg rounded-xl border border-outline-variant/30 shadow-lg p-3.5 pr-8 flex items-start gap-2.5 relative overflow-hidden bg-surface/80 border-l-4 ' + TOAST_BORDER[t];
    el.innerHTML = `
        <span class="material-symbols-outlined shrink-0 mt-px ${TOAST_ICON_COLOR[t]}" style="font-size:20px">${TOAST_ICONS[t]}</span>
        <p class="text-sm text-on-surface flex-1 leading-snug break-words font-medium">${message}</p>
        <button onclick="this.parentElement?._dismiss()" class="absolute top-2 right-2 text-on-surface-variant/50 hover:text-on-surface shrink-0">
            <span class="material-symbols-outlined" style="font-size:16px">close</span>
        </button>
        <div class="toast-progress-bar absolute bottom-0 left-0 h-[3px] rounded-b-xl opacity-40" style="background:currentColor"></div>
    `;
    el._dismiss = () => { el.remove(); };

    const bar = el.querySelector('.toast-progress-bar');
    bar.style.transition = `width ${duration}ms linear`;
    requestAnimationFrame(() => { bar.style.width = '100%'; });
    setTimeout(() => { bar.style.width = '0%'; }, 20);

    container.appendChild(el);

    const dismiss = () => { el.style.opacity = '0'; el.style.transform = 'translateX(120%)'; el.style.transition = 'all .25s ease-in'; setTimeout(() => el.remove(), 250); };
    setTimeout(dismiss, duration);
};

window.showConfirm = function (message, title = 'Konfirmasi') {
    return new Promise((resolve) => {
        const id = 'confirm-' + Date.now();
        const wrapper = document.createElement('div');
        wrapper.id = id;
        wrapper.className = 'fixed inset-0 z-[9998] flex items-center justify-center p-4';
        wrapper.innerHTML = `
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div class="relative bg-surface/95 backdrop-blur-xl rounded-2xl border border-outline-variant shadow-xl max-w-md w-full p-6 transform scale-95 opacity-0 transition-all duration-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-amber-600">help</span>
                    </div>
                    <h3 class="text-title-md font-bold text-on-surface">${title}</h3>
                </div>
                <p class="text-body-md text-on-surface-variant mb-6">${message}</p>
                <div class="flex justify-end gap-3">
                    <button id="${id}-cancel" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-bold text-sm hover:bg-surface-container transition-colors">Batal</button>
                    <button id="${id}-confirm" class="px-5 py-2.5 rounded-lg bg-error text-white font-bold text-sm hover:bg-error/90 transition-colors shadow-sm">Ya, Lanjutkan</button>
                </div>
            </div>
        `;
        document.body.appendChild(wrapper);

        const card = wrapper.querySelector('.relative');
        requestAnimationFrame(() => { wrapper.querySelector('.absolute').style.opacity = '1'; card.style.opacity = '1'; card.style.transform = 'scale(1)'; });

        const cleanup = (result) => { wrapper.remove(); resolve(result); };
        document.getElementById(`${id}-cancel`).onclick = () => cleanup(false);
        document.getElementById(`${id}-confirm`).onclick = () => cleanup(true);
        wrapper.querySelector('.absolute').onclick = () => cleanup(false);
    });
};

document.addEventListener('submit', function (e) {
    const form = e.target.closest('form');
    if (!form) return;
    const msg = form.dataset.confirm;
    if (!msg) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    window.showConfirm(msg).then(function (ok) {
        if (ok) {
            form.removeAttribute('data-confirm');
            requestAnimationFrame(() => form.submit());
        }
    });
}, true);

document.addEventListener('DOMContentLoaded', function () {
    const b = document.body;
    const s = b.dataset.flashSuccess;
    const e = b.dataset.flashError;
    if (s) { delete b.dataset.flashSuccess; showToast('success', s); }
    if (e) { delete b.dataset.flashError; showToast('error', e, 7000); }
});

window.Alpine = Alpine;
Alpine.start();
