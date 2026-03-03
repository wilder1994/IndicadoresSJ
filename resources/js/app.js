import './bootstrap';

document.addEventListener('livewire:init', () => {
    const key = 'lw_expired_reload_at';
    const cooldownMs = 10000;

    const reloadOnce = () => {
        const now = Date.now();
        const last = Number(sessionStorage.getItem(key) || 0);

        if (now - last < cooldownMs) {
            return;
        }

        sessionStorage.setItem(key, String(now));
        window.location.reload();
    };

    if (typeof window.Livewire?.onPageExpired === 'function') {
        window.Livewire.onPageExpired(() => {
            reloadOnce();
            return false;
        });
    }
});
