document.addEventListener("DOMContentLoaded", () => {
    console.log("AutoComplete.js carregado");

    function initAutoComplete(inputClass, hiddenClass, tipo) {
        console.log(`Inicializando autocomplete para: ${inputClass}, tipo: ${tipo}`);

        document.addEventListener("input", function (e) {
            // Verifica se o input é do tipo correto
            if (!e.target.classList.contains(inputClass)) {
                return;
            }

            const input = e.target;
            const wrapper = input.closest(".autocomplete-wrapper");
            const box = wrapper.querySelector(".suggestions");
            const hidden = wrapper.querySelector(hiddenClass);

            const termo = input.value.trim();
            console.log(`Digitando: ${termo}, Tipo: ${tipo}`);

            if (termo.length < 2) {
                box.innerHTML = "";
                box.style.display = "none";
                hidden.value = "";
                return;
            }

            // DEBUG: Verificar se o fetch está sendo chamado
            console.log(`Fazendo fetch para: ${termo}`);

            // IMPORTANTE: Verifique o caminho correto
            // O RegisterProject.php está em: View/RegisterProject.php
            // O SearchUsers.php está em: Config/SearchUsers.php
            // Caminho relativo: ../Config/SearchUsers.php
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
                console.log("Resposta recebida, status:", res.status);
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(dados => {
                console.log("Dados recebidos:", dados);

                box.innerHTML = "";

                if (!dados.length) {
                    console.log("Nenhum dado encontrado");
                    box.style.display = "none";
                    return;
                }

                dados.forEach(item => {
                    const div = document.createElement("div");
                    div.textContent = item.nome;
                    div.className = "suggestion-item";
                    
                    // Estilo básico para as sugestões
                    div.style.padding = "8px";
                    div.style.cursor = "pointer";
                    div.style.borderBottom = "1px solid #eee";
                    
                    div.addEventListener("mouseenter", () => {
                        div.style.backgroundColor = "#f0f0f0";
                    });
                    
                    div.addEventListener("mouseleave", () => {
                        div.style.backgroundColor = "white";
                    });

                    div.addEventListener("click", () => {
                        console.log(`Item selecionado: ${item.nome} (ID: ${item.id})`);
                        input.value = item.nome;
                        hidden.value = item.id;
                        box.innerHTML = "";
                        box.style.display = "none";
                    });

                    box.appendChild(div);
                });

                box.style.display = "block";
                
                // Estilo para a caixa de sugestões
                box.style.position = "absolute";
                box.style.zIndex = "1000";
                box.style.backgroundColor = "white";
                box.style.border = "1px solid #ccc";
                box.style.width = "100%";
                box.style.maxHeight = "200px";
                box.style.overflowY = "auto";
            })
            .catch(err => {
                console.error("Erro no autocomplete:", err);
                box.innerHTML = "<div style='padding: 8px; color: red;'>Erro ao carregar sugestões</div>";
                box.style.display = "block";
            });
        });

        // Fechar sugestões ao clicar fora
        document.addEventListener("click", function (e) {
            if (!e.target.classList.contains(inputClass)) {
                document.querySelectorAll(".suggestions").forEach(box => {
                    box.style.display = "none";
                });
            }
        });
    }

    // Inicializa quando o DOM estiver completamente carregado
    setTimeout(() => {
        console.log("Inicializando autocomplete...");
        // Correção: passe as classes sem o ponto
        initAutoComplete("aluno-input", ".aluno-id", "student");
        initAutoComplete("professor-input", ".professor-id", "teacher");
        
        // Verifica se os inputs existem
        console.log("Inputs alunos encontrados:", document.querySelectorAll(".aluno-input").length);
        console.log("Inputs professores encontrados:", document.querySelectorAll(".professor-input").length);
    }, 100);
});