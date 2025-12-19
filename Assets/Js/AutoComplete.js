document.addEventListener("input", function (e) {

    if (!e.target.classList.contains("autocomplete-input")) return;

    const input = e.target;
    const box = input.closest(".autocomplete-wrapper")
                     .querySelector(".suggestions");

    const termo = input.value;

    if (termo.length < 1) {
        box.innerHTML = "";
        box.style.display = "none";
        return;
    }

    const tipo = input.classList.contains("aluno-input") ? "Aluno" : "Professor";

    fetch("/Innovatech/Config/SearchUsers.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `termo=${encodeURIComponent(termo)}&tipo=${tipo}`
    })
    .then(res => res.json())
    .then(dados => {
        box.innerHTML = "";
        box.style.display = "block";

        dados.forEach(nome => {
            const div = document.createElement("div");
            div.textContent = nome;
            div.onclick = () => {
                input.value = nome;
                box.innerHTML = "";
                box.style.display = "none";
            };
            box.appendChild(div);
        });
    });
});
