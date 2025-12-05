// Carregar o footer dinamicamente
fetch("../Includes/Footer.html")
.then(response => response.text())
.then(data => {
    document.getElementById("footer").innerHTML = data;
})
.catch(error => console.error("Erro ao carregar o rodapé:", error));