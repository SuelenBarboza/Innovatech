// Carregar o cabeçalho dinamicamente
fetch("Header.html")
    .then(response => response.text())
    .then(data => {
        document.getElementById("header").innerHTML = data;

        // Forçar o navegador a carregar a versão mais recente do Notification.js
        let script = document.createElement("script");
        script.src = "js/Notification.js?v=" + new Date().getTime();
        document.body.appendChild(script);
    })
    .catch(error => console.error("Erro ao carregar o cabeçalho:", error));
