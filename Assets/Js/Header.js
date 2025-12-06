// // Carregar o header dinamicamente
// fetch('../../Includes/Header.html')
//     .then(response => response.text())
//     .then(data => {
//         document.getElementById("header").innerHTML = data;

//         let script = document.createElement("script");
//         script.src = "/Assets/Js/Notification.js?v=" + new Date().getTime();
//         script.onload = () => {
//             console.log("Notification.js carregado com sucesso!");
//         };
//         document.body.appendChild(script);
//     })
//     .catch(error => console.error("Erro ao carregar o cabeçalho:", error));

async function loadHeader() {
    const header = document.getElementById("header");
    try {
        const response = await fetch("../Includes/Header.html");
        header.innerHTML = await response.text();
    } catch (error) {
        console.error("Erro ao carregar o cabeçalho:", error);
    }
}

document.addEventListener("DOMContentLoaded", loadHeader);