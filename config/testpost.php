document.addEventListener("DOMContentLoaded", function() {
    console.log("✅ AutoComplete.js carregado!");
    
    // CAMINHO CORRETO - baseado no seu teste
    // O RegisterProject.php está em: View/RegisterProject.php
    // O SearchUsers.php está em: Config/SearchUsers.php
    const BASE_PATH = '../Config/SearchUsers.php'; // Este é o caminho CORRETO
    
    console.log("📁 Caminho configurado:", BASE_PATH);
    
    // FUNÇÃO PRINCIPAL
    function initAutocomplete(inputClass, hiddenClass, userType) {
        console.log(`🎯 Configurando autocomplete para: ${inputClass} (${userType})`);
        
        // Captura TODOS os inputs da página
        document.querySelectorAll(inputClass).forEach(input => {
            console.log(`🔍 Encontrado input:`, input);
            
            const wrapper = input.closest('.autocomplete-wrapper');
            if (!wrapper) {
                console.error("❌ Não encontrou .autocomplete-wrapper para:", input);
                return;
            }
            
            const suggestionsBox = wrapper.querySelector('.suggestions');
            const hiddenInput = wrapper.querySelector(hiddenClass);
            
            if (!suggestionsBox || !hiddenInput) {
                console.error("❌ Elementos não encontrados no wrapper");
                return;
            }
            
            // Adiciona evento de input
            input.addEventListener('input', function(e) {
                const searchTerm = this.value.trim();
                console.log(`📝 Digitado: "${searchTerm}"`);
                
                // Limpa se tiver menos de 2 caracteres
                if (searchTerm.length < 2) {
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.style.display = 'none';
                    hiddenInput.value = '';
                    return;
                }
                
                // Faz a requisição
                console.log(`🔄 Buscando em: ${BASE_PATH}`);
                
                fetch(BASE_PATH, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `termo=${encodeURIComponent(searchTerm)}&tipo=${userType}`
                })
                .then(response => {
                    console.log("📡 Resposta status:", response.status);
                    if (!response.ok) {
                        throw new Error(`Erro HTTP: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`📊 ${data.length} resultados encontrados:`, data);
                    
                    suggestionsBox.innerHTML = '';
                    
                    if (data.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.textContent = 'Nenhum resultado encontrado';
                        noResults.style.padding = '8px';
                        noResults.style.color = '#666';
                        suggestionsBox.appendChild(noResults);
                    } else {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.textContent = item.nome;
                            div.setAttribute('data-id', item.id);
                            
                            // Estilo
                            div.style.padding = '10px';
                            div.style.cursor = 'pointer';
                            div.style.borderBottom = '1px solid #eee';
                            div.style.transition = 'background 0.2s';
                            
                            // Efeito hover
                            div.addEventListener('mouseenter', () => {
                                div.style.backgroundColor = '#007bff';
                                div.style.color = 'white';
                            });
                            
                            div.addEventListener('mouseleave', () => {
                                div.style.backgroundColor = '';
                                div.style.color = '';
                            });
                            
                            // Selecionar
                            div.addEventListener('click', function() {
                                console.log(`👉 Selecionado: ${item.nome} (ID: ${item.id})`);
                                input.value = item.nome;
                                hiddenInput.value = item.id;
                                suggestionsBox.style.display = 'none';
                            });
                            
                            suggestionsBox.appendChild(div);
                        });
                    }
                    
                    suggestionsBox.style.display = 'block';
                })
                .catch(error => {
                    console.error('❌ Erro no fetch:', error);
                    suggestionsBox.innerHTML = `
                        <div style="padding: 10px; color: red; background: #ffe6e6;">
                            Erro: ${error.message}
                        </div>
                    `;
                    suggestionsBox.style.display = 'block';
                });
            });
            
            // Fechar sugestões ao clicar fora
            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    suggestionsBox.style.display = 'none';
                }
            });
        });
    }
    
    // AGUARDA um pouco para garantir que o DOM está pronto
    setTimeout(() => {
        console.log("🚀 Inicializando autocompletes...");
        
        // VERIFICA se os elementos existem
        const alunoInputs = document.querySelectorAll('.aluno-input');
        const professorInputs = document.querySelectorAll('.professor-input');
        
        console.log(`👨‍🎓 Inputs aluno encontrados: ${alunoInputs.length}`);
        console.log(`👨‍🏫 Inputs professor encontrados: ${professorInputs.length}`);
        
        // INICIALIZA
        initAutocomplete('.aluno-input', '.aluno-id', 'student');
        initAutocomplete('.professor-input', '.professor-id', 'teacher');
        
        console.log("✅ Autocompletes inicializados!");
        
        // TESTE AUTOMÁTICO (opcional - remove depois)
        setTimeout(() => {
            const primeiroAlunoInput = document.querySelector('.aluno-input');
            if (primeiroAlunoInput) {
                console.log("🧪 Disparando teste automático...");
                primeiroAlunoInput.focus();
                primeiroAlunoInput.value = 'Ana';
                const event = new Event('input', { bubbles: true });
                primeiroAlunoInput.dispatchEvent(event);
            }
        }, 1000);
        
    }, 500);
});