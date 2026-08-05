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
        wrapper.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
        wrapper.innerHTML = `
            <div class="absolute inset-0 bg-black/60 transition-opacity"></div>
            <div class="relative bg-surface rounded-2xl shadow-2xl w-full p-6 border border-outline-variant text-center transform scale-95 opacity-0 transition-all duration-200" style="max-width: 380px;">
                <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 shadow-xs" style="background-color: #fee2e2; color: #dc2626;">
                    <span class="material-symbols-outlined text-2xl">help_outline</span>
                </div>
                <h3 class="text-headline-sm font-bold text-on-surface mb-1">${title}</h3>
                <p class="text-xs text-on-surface-variant mb-5 leading-relaxed">${message}</p>
                <div class="flex justify-center gap-3 pt-3 border-t border-outline-variant/40">
                    <button id="${id}-cancel" type="button" class="px-4 py-2 text-xs font-bold text-on-surface-variant bg-surface hover:bg-surface-container border border-outline-variant rounded-lg transition-colors">Batal</button>
                    <button id="${id}-confirm" type="button" class="px-5 py-2 text-xs font-bold text-white rounded-lg shadow-sm transition-colors" style="background-color: #dc2626;">Ya, Lanjutkan</button>
                </div>
            </div>
        `;
        document.body.appendChild(wrapper);

        const card = wrapper.querySelector('.relative');
        requestAnimationFrame(() => { card.style.opacity = '1'; card.style.transform = 'scale(1)'; });

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
