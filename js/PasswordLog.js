// Aqui é página para esconder a senha da parte de login.

document.getElementById("mostrarSenha").addEventListener("click", function () {
    let senhaInput = document.getElementById("passwordL");
    this.classList.toggle("fa-eye-slash");

    if (senhaInput.type === "password") {
        senhaInput.type = "text";
    } else {
        senhaInput.type = "password";
    }
});

document.querySelector("form").addEventListener("submit", function(event) {
window.location.href = "Home.html"; 
event.preventDefault(); 
});
