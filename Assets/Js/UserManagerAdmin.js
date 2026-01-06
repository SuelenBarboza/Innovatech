//Função para busca e filtro na tabela de usuários da pagina de gerenciamento de usuários (Admin)
document.addEventListener('DOMContentLoaded', function() {
    // Elementos da busca
    const barraBusca = document.getElementById('barra-busca');
    const filtroTipo = document.getElementById('filtro-tipo');
    const limparBusca = document.getElementById('limpar-busca');
    const tabelaCorpo = document.getElementById('tabela-corpo');
    const contadorResultados = document.getElementById('contador-resultados');
    const linhasUsuarios = tabelaCorpo.querySelectorAll('tr');
    
    // Filtros rápidos
    const filtrosRapidos = document.querySelectorAll('.filtro-btn');
    filtrosRapidos.forEach(filtro => {
        filtro.addEventListener('click', function() {
            const tipo = this.getAttribute('data-tipo');
            if (filtroTipo.value === tipo) {
                filtroTipo.value = '';
                this.classList.remove('ativo');
            } else {
                filtroTipo.value = tipo;
                filtrosRapidos.forEach(f => f.classList.remove('ativo'));
                this.classList.add('ativo');
            }
            filtrarTabela();
        });
    });
    
    // Filtrar tabela
    function filtrarTabela() {
        const termoBusca = barraBusca.value.toLowerCase();
        const tipoFiltro = filtroTipo.value;
        let resultadosEncontrados = 0;
        
        linhasUsuarios.forEach(linha => {
            const nome = linha.querySelector('.col-nome').textContent.toLowerCase();
            const email = linha.querySelector('.col-email').textContent.toLowerCase();
            const tipo = linha.querySelector('.tipo-badge').textContent;
            let deveMostrar = true;
            
            // Aplicar filtro de texto
            if (termoBusca) {
                deveMostrar = nome.includes(termoBusca) || email.includes(termoBusca);
            }
            
            // Aplicar filtro por tipo
            if (deveMostrar && tipoFiltro) {
                deveMostrar = tipo === tipoFiltro;
            }
            
            if (deveMostrar) {
                linha.style.display = '';
                resultadosEncontrados++;
                
                // Destacar termo de busca
                if (termoBusca) {
                    linha.classList.add('destaque-busca');
                } else {
                    linha.classList.remove('destaque-busca');
                }
            } else {
                linha.style.display = 'none';
                linha.classList.remove('destaque-busca');
            }
        });
        
        // Atualizar contador
        contadorResultados.textContent = resultadosEncontrados;
    }
    
    // Event listeners
    barraBusca.addEventListener('input', filtrarTabela);
    filtroTipo.addEventListener('change', filtrarTabela);
    
    // Limpar busca
    limparBusca.addEventListener('click', function() {
        barraBusca.value = '';
        filtroTipo.value = '';
        filtrosRapidos.forEach(f => f.classList.remove('ativo'));
        filtrarTabela();
        barraBusca.focus();
    });
    
    // Ordenação de colunas (opcional, apenas para visualização local)
    const cabecalhos = document.querySelectorAll('.tabela-usuarios th[data-ordenar]');
    cabecalhos.forEach(cabecalho => {
        cabecalho.addEventListener('click', function() {
            const coluna = this.getAttribute('data-ordenar');
            const direcao = this.getAttribute('data-direcao') === 'asc' ? 'desc' : 'asc';
            
            // Resetar ícones
            cabecalhos.forEach(c => {
                c.removeAttribute('data-direcao');
                c.querySelector('.ordenacao').textContent = '';
            });
            
            // Definir nova direção
            this.setAttribute('data-direcao', direcao);
            this.querySelector('.ordenacao').textContent = direcao === 'asc' ? '↑' : '↓';
            
            // Ordenar localmente (apenas os itens visíveis)
            ordenarTabelaLocal(coluna, direcao);
        });
    });
    
    function ordenarTabelaLocal(coluna, direcao) {
        const linhasVisiveis = Array.from(linhasUsuarios).filter(linha => linha.style.display !== 'none');
        
        linhasVisiveis.sort((a, b) => {
            let valorA, valorB;
            
            switch(coluna) {
                case 'nome':
                    valorA = a.querySelector('.col-nome').textContent;
                    valorB = b.querySelector('.col-nome').textContent;
                    break;
                case 'email':
                    valorA = a.querySelector('.col-email').textContent;
                    valorB = b.querySelector('.col-email').textContent;
                    break;
                case 'data':
                    valorA = new Date(a.querySelector('.col-data').getAttribute('data-data'));
                    valorB = new Date(b.querySelector('.col-data').getAttribute('data-data'));
                    break;
                case 'tipo':
                    valorA = a.querySelector('.tipo-badge').textContent;
                    valorB = b.querySelector('.tipo-badge').textContent;
                    break;
                default:
                    return 0;
            }
            
            if (direcao === 'asc') {
                return valorA > valorB ? 1 : -1;
            } else {
                return valorA < valorB ? 1 : -1;
            }
        });
        
        // Reordenar apenas as linhas visíveis
        linhasVisiveis.forEach(linha => tabelaCorpo.appendChild(linha));
    }
    
    // Controle de itens por página
    const itensPorPaginaSelect = document.getElementById('itens-por-pagina-select');
    if (itensPorPaginaSelect) {
        itensPorPaginaSelect.addEventListener('change', function() {
            const novoLimite = this.value;
            window.location.href = `?pagina=1&limite=${novoLimite}`;
        });
    }
});