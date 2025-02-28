// Aqui é página para a notificação funcionar

function toggleNotificationMenu() {
    const notificationMenu = document.querySelector('.notification-menu');
    const notificationBubble = document.querySelector('.notification-bubble');
    
    
    const isVisible = notificationMenu.style.display === 'block';

    if (isVisible) {
        notificationMenu.style.display = 'none';
        notificationBubble.style.display = 'none'; 
    } else {
        notificationMenu.style.display = 'block';
        notificationBubble.style.display = 'block'; 
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
}


const observer = new MutationObserver((mutationsList, observer) => {
    const header = document.getElementById('header');
    if (header && header.innerHTML.trim() !== '') {
        setupNotificationEvents();
        observer.disconnect(); 
    }
});

observer.observe(document.body, { childList: true, subtree: true });


setTimeout(() => {
    document.querySelector('.notification-bubble').style.display = 'block'; // Exibe a bolinha
}, 3000);