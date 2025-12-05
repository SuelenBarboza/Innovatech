// Função para alternar a visibilidade da senha
function toggleSenha(inputId, iconId) {
    let senhaInput = document.getElementById(inputId);
    let icon = document.getElementById(iconId);

    if (senhaInput.type === "password") {
        senhaInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        senhaInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

// Adiciona o evento ao botão do olho
document.getElementById("mostrarSenha").addEventListener("click", function () {
    toggleSenha("passwordL", "mostrarSenha");
});
