// Essa página é para a parte de  notificação 

// Função para alternar a visibilidade do menu de notificações
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

// Evento para exibir ou ocultar o menu ao clicar no sino
document.querySelector('.bell-icon').addEventListener('click', toggleNotificationMenu);

// Adiciona animação para remover a notificação ao clicar
document.querySelectorAll('.notification').forEach(notification => {
    notification.addEventListener('click', function () {
        this.classList.add('fade-out'); // Adiciona a classe para animação
        setTimeout(() => {
            this.remove(); // Remove do DOM após a animação
        }, 500); // Tempo igual ao da animação no CSS
    });
});

// Simula o recebimento de uma nova notificação após 3 segundos
setTimeout(() => {
    document.querySelector('.notification-bubble').style.display = 'block'; // Exibe a bolinha
}, 3000);
