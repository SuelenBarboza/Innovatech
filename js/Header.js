fetch("Header.html")
    .then(response => response.text())
    .then(data => {
        document.getElementById("header").innerHTML = data;

        // Carregar o Notification.js após o cabeçalho ser carregado
        let script = document.createElement("script");
        script.src = "js/Notification.js?v=" + new Date().getTime();
        script.onload = () => {
            console.log("Notification.js carregado com sucesso!");
        };
        document.body.appendChild(script);
    })
    .catch(error => console.error("Erro ao carregar o cabeçalho:", error));