const userId = document.querySelector('meta[name="user-id"]')?.content;

function showToast(message) {
    const toast = document.getElementById('toast');
    if (!toast) return;

    toast.textContent = message;
    toast.classList.remove('hidden');

    clearTimeout(showToast.timer);
    showToast.timer = setTimeout(() => toast.classList.add('hidden'), 4500);
}

if (userId && window.Echo) {
    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.notification.received', (e) => {
            // Bell badge
            const badge = document.querySelector('[data-notif-badge]');
            if (badge) {
                badge.textContent = e.unread > 9 ? '9+' : e.unread;
                badge.classList.toggle('hidden', e.unread < 1);
            }

            // Add to the dropdown list
            const list = document.querySelector('[data-notif-list]');
            const template = document.getElementById('notification-item-template');

            if (list && template) {
                list.querySelector('[data-notif-empty]')?.remove();

                const item = template.content.firstElementChild.cloneNode(true);
                item.href = e.url;
                item.querySelector('[data-message]').textContent = e.message;
                list.prepend(item);
            }

            showToast(e.message);
        });
}