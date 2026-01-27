//Filtra a pagina da lista de projetos do admin 
document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector(".filtros");
    if (!form) return;

    const campos = form.querySelectorAll("input, select");

    campos.forEach(campo => {
        campo.addEventListener("change", () => {
            form.submit();
        });
    });

});
