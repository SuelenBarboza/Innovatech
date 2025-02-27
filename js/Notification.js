function toggleNotificationMenu() {
    const notificationMenu = document.querySelector('.notification-menu');
    const notificationBubble = document.querySelector('.notification-bubble');
    
    // Verifica se o menu está visível
    const isVisible = notificationMenu.style.display === 'block';

    if (isVisible) {
        notificationMenu.style.display = 'none';
        notificationBubble.style.display = 'none'; // Esconde a bolinha
    } else {
        notificationMenu.style.display = 'block';
        notificationBubble.style.display = 'block'; // Exibe a bolinha
    }
}

function setupNotificationEvents() {
    const bellIcon = document.querySelector('.bell-icon');
    if (bellIcon) {
        bellIcon.addEventListener('click', toggleNotificationMenu);
    }

    document.querySelectorAll('.notification').forEach(notification => {
        notification.addEventListener('click', function () {
            this.classList.add('fade-out'); // Adiciona a classe para animação
            setTimeout(() => {
                this.remove(); // Remove do DOM após a animação
            }, 500); // Tempo igual ao da animação no CSS
        });
    });
}

// Observar mudanças no DOM para garantir que o cabeçalho foi carregado
const observer = new MutationObserver((mutationsList, observer) => {
    const header = document.getElementById('header');
    if (header && header.innerHTML.trim() !== '') {
        setupNotificationEvents();
        observer.disconnect(); // Parar de observar após o cabeçalho ser carregado
    }
});

// Iniciar a observação no elemento que contém o cabeçalho
observer.observe(document.body, { childList: true, subtree: true });

// Simula o recebimento de uma nova notificação após 3 segundos
setTimeout(() => {
    document.querySelector('.notification-bubble').style.display = 'block'; // Exibe a bolinha
}, 3000);