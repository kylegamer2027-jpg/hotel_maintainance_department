document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert').forEach((alert) => {
        window.setTimeout(() => {
            alert.classList.add('alert-dismissed');
            window.setTimeout(() => alert.remove(), 220);
        }, 6000);
    });

    document.querySelectorAll('input[type="search"]').forEach((input) => {
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') input.value = '';
        });
    });
});
