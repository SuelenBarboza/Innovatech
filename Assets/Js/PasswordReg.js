// Aqui é página para esconder a senha da parte de registrar.

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

document.getElementById("mostrarSenha").addEventListener("click", function () {
    toggleSenha("password", "mostrarSenha"); // Corrigido
});

document.getElementById("mostrarConfirmarSenha").addEventListener("click", function () {
    toggleSenha("confirmPassword", "mostrarConfirmarSenha"); // Corrigido
});
