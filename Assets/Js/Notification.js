// Carregar a notificação dinamicamente

function toggleNotificationMenu() {
    const notificationMenu = document.querySelector('.notification-menu');
    const notificationBubble = document.querySelector('.notification-bubble');

    if (notificationMenu && notificationBubble) {
        const isVisible = notificationMenu.style.display === 'block';

        notificationMenu.style.display = isVisible ? 'none' : 'block';
        notificationBubble.style.display = isVisible ? 'none' : 'block';
    }
}

function setupNotificationEvents() {
    const bellIcon = document.querySelector('.bell-icon');
    if (bellIcon) {
        bellIcon.addEventListener('click', toggleNotificationMenu);
    }

    document.querySelectorAll('.notification').forEach(notification => {
        notification.addEventListener('click', function () {
            this.classList.add('fade-out');
            setTimeout(() => {
                this.remove();
            }, 500);
        });
    });

    const notificationBubble = document.querySelector('.notification-bubble');
    if (notificationBubble) {
        setTimeout(() => {
            notificationBubble.style.display = 'block';
        }, 3000);
    }
}

const observer = new MutationObserver((mutationsList, observer) => {
    const header = document.getElementById('header');
    const bellIcon = document.querySelector('.bell-icon');

    if (header && header.innerHTML.trim() !== '' && bellIcon) {
        setupNotificationEvents();
        observer.disconnect(); 
    }
});

observer.observe(document.body, { childList: true, subtree: true });

setTimeout(() => {
    const bellIcon = document.querySelector('.bell-icon');
    if (bellIcon) {
        console.log("Fallback: evento adicionado via setTimeout");
        setupNotificationEvents();
    } else {
        console.log("Fallback: bellIcon ainda não encontrado.");
    }
}, 2000);

