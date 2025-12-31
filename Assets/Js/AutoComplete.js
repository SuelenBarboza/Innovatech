document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM carregado - JS principal iniciado");

    /* =====================================================
       AUTOCOMPLETE de ALUNOS e PROFESSORES
    ===================================================== */

    function initAutoComplete(inputClass, hiddenClass, tipo) {
        console.log(`Autocomplete iniciado: ${inputClass} | tipo: ${tipo}`);

        // Evento de digitação (delegado)
        document.addEventListener("input", (e) => {
            if (!e.target.classList.contains(inputClass)) return;

            const input = e.target;
            const wrapper = input.closest(".autocomplete-wrapper");
            if (!wrapper) return;

            const box = wrapper.querySelector(".suggestions");
            const hidden = wrapper.querySelector(hiddenClass);

            const termo = input.value.trim();

            if (termo.length < 2) {
                box.innerHTML = "";
                box.style.display = "none";
                hidden.value = "";
                return;
            }

            fetch("../Config/SearchUsers.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    termo: termo,
                    tipo: tipo
                })
            })
            .then(res => {
                if (!res.ok) throw new Error("Erro HTTP");
                return res.json();
            })
            .then(dados => {
                box.innerHTML = "";

                if (!dados || dados.length === 0) {
                    box.style.display = "none";
                    return;
                }

                dados.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "suggestion-item";
                    div.textContent = item.nome;

                    // Estilo básico
                    div.style.padding = "8px";
                    div.style.cursor = "pointer";
                    div.style.borderBottom = "1px solid #eee";

                    div.addEventListener("mouseenter", () => {
                        div.style.backgroundColor = "#f0f0f0";
                    });

                    div.addEventListener("mouseleave", () => {
                        div.style.backgroundColor = "#fff";
                    });

                    div.addEventListener("click", () => {
                        input.value = item.nome;
                        hidden.value = item.id;
                        box.innerHTML = "";
                        box.style.display = "none";
                    });

                    box.appendChild(div);
                });

                box.style.display = "block";
                box.style.position = "absolute";
                box.style.zIndex = "1000";
                box.style.backgroundColor = "#fff";
                box.style.border = "1px solid #ccc";
                box.style.width = "100%";
                box.style.maxHeight = "200px";
                box.style.overflowY = "auto";
            })
            .catch(err => {
                console.error("Erro no autocomplete:", err);
                box.innerHTML = "<div style='padding:8px;color:red'>Erro ao buscar dados</div>";
                box.style.display = "block";
            });
        });

        // Fecha sugestões ao clicar fora
        document.addEventListener("click", (e) => {
            if (!e.target.classList.contains(inputClass)) {
                document.querySelectorAll(".suggestions").forEach(box => {
                    box.style.display = "none";
                });
            }
        });
    }

    /* =====================================================
       INICIALIZAÇÃO
    ===================================================== */

    initAutoComplete("aluno-input", ".aluno-id", "student");
    initAutoComplete("professor-input", ".professor-id", "teacher");

    console.log("Inputs alunos encontrados:", document.querySelectorAll(".aluno-input").length);
    console.log("Inputs professores encontrados:", document.querySelectorAll(".professor-input").length);
});
