//Função para busca e filtro na tabela de usuários da pagina de gerenciamento de usuários (Coordenador)

document.addEventListener('DOMContentLoaded', function () {
    console.log('Script carregado - iniciando...');

    // Elementos da busca
    const barraBusca = document.getElementById('barra-busca');
    const filtroTipo = document.getElementById('filtro-tipo');
    const limparBusca = document.getElementById('limpar-busca');
    const tabelaCorpo = document.getElementById('tabela-corpo');
    const contadorResultados = document.getElementById('contador-resultados');
    const totalResultados = document.getElementById('total-resultados');
    const filtrosRapidos = document.querySelectorAll('.filtro-btn');

    // Verificar se os elementos existem
    if (!barraBusca || !filtroTipo || !limparBusca || !tabelaCorpo || !contadorResultados) {
        console.error('Elementos não encontrados:', {
            barraBusca: !!barraBusca,
            filtroTipo: !!filtroTipo,
            limparBusca: !!limparBusca,
            tabelaCorpo: !!tabelaCorpo,
            contadorResultados: !!contadorResultados
        });
        return;
    }

    console.log('Elementos encontrados, inicializando...');

    // Obter todas as linhas da tabela
    function getLinhas() {
        return Array.from(tabelaCorpo.querySelectorAll('tr'));
    }

    // Função para filtrar a tabela
    function filtrarTabela() {
        console.log('Filtrando tabela...');
        const termo = barraBusca.value.toLowerCase();
        const tipoSelecionado = filtroTipo.value;
        let visiveis = 0;

        getLinhas().forEach(linha => {
            const nomeElem = linha.querySelector('.col-nome');
            const emailElem = linha.querySelector('.col-email');
            const tipoElem = linha.querySelector('.tipo-badge');
            
            // Verificar se os elementos existem
            if (!nomeElem || !emailElem || !tipoElem) {
                linha.style.display = 'none';
                return;
            }
            
            const nome = nomeElem.textContent.toLowerCase();
            const email = emailElem.textContent.toLowerCase();
            const tipo = tipoElem.textContent;

            let mostrar = true;

            // Filtrar por termo de busca
            if (termo && !nome.includes(termo) && !email.includes(termo)) {
                mostrar = false;
            }

            // Filtrar por tipo
            if (mostrar && tipoSelecionado && tipo !== tipoSelecionado) {
                mostrar = false;
            }

            // Aplicar estilo
            linha.style.display = mostrar ? '' : 'none';
            
            // Destacar se encontrado
            if (mostrar && termo) {
                linha.classList.add('destaque-busca');
            } else {
                linha.classList.remove('destaque-busca');
            }

            if (mostrar) visiveis++;
        });

        // Atualizar contador
        contadorResultados.textContent = visiveis;
        console.log('Resultados visíveis:', visiveis);
    }

    // Event listeners para busca
    barraBusca.addEventListener('input', filtrarTabela);
    filtroTipo.addEventListener('change', filtrarTabela);

    // Filtros rápidos
    filtrosRapidos.forEach(btn => {
        btn.addEventListener('click', function () {
            const tipo = this.dataset.tipo;
            console.log('Filtro rápido clicado:', tipo);

            if (filtroTipo.value === tipo) {
                filtroTipo.value = '';
                this.classList.remove('ativo');
            } else {
                filtroTipo.value = tipo;
                filtrosRapidos.forEach(b => b.classList.remove('ativo'));
                this.classList.add('ativo');
            }

            filtrarTabela();
        });
    });

    // Limpar busca
    limparBusca.addEventListener('click', function () {
        console.log('Limpando busca...');
        barraBusca.value = '';
        filtroTipo.value = '';
        filtrosRapidos.forEach(b => b.classList.remove('ativo'));
        filtrarTabela();
        barraBusca.focus();
    });

    // Ordenação local
    const cabecalhosOrdenaveis = document.querySelectorAll('.tabela-usuarios th[data-ordenar]');
    cabecalhosOrdenaveis.forEach(th => {
        th.addEventListener('click', function () {
            const coluna = this.dataset.ordenar;
            const direcaoAtual = this.dataset.direcao;
            const direcao = direcaoAtual === 'asc' ? 'desc' : 'asc';
            
            console.log('Ordenando por:', coluna, direcao);

            // Limpar direção de todos os cabeçalhos
            cabecalhosOrdenaveis.forEach(c => {
                c.dataset.direcao = '';
                const ordenacaoSpan = c.querySelector('.ordenacao');
                if (ordenacaoSpan) ordenacaoSpan.textContent = '';
            });

            // Definir nova direção
            this.dataset.direcao = direcao;
            const ordenacaoSpan = this.querySelector('.ordenacao');
            if (ordenacaoSpan) {
                ordenacaoSpan.textContent = direcao === 'asc' ? '↑' : '↓';
            }

            ordenarTabela(coluna, direcao);
        });
    });

    // Função de ordenação
    function ordenarTabela(coluna, direcao) {
        console.log('Ordenando tabela...', coluna, direcao);
        
        // Obter apenas linhas visíveis
        const linhas = getLinhas().filter(l => l.style.display !== 'none');
        
        if (linhas.length === 0) return;

        linhas.sort((a, b) => {
            let valorA, valorB;

            switch (coluna) {
                case 'nome':
                    const nomeA = a.querySelector('.col-nome');
                    const nomeB = b.querySelector('.col-nome');
                    valorA = nomeA ? nomeA.textContent : '';
                    valorB = nomeB ? nomeB.textContent : '';
                    break;
                case 'email':
                    const emailA = a.querySelector('.col-email');
                    const emailB = b.querySelector('.col-email');
                    valorA = emailA ? emailA.textContent : '';
                    valorB = emailB ? emailB.textContent : '';
                    break;
                case 'data':
                    const dataA = a.querySelector('.col-data');
                    const dataB = b.querySelector('.col-data');
                    valorA = dataA ? new Date(dataA.dataset.data) : new Date(0);
                    valorB = dataB ? new Date(dataB.dataset.data) : new Date(0);
                    break;
                case 'tipo':
                    const tipoA = a.querySelector('.tipo-badge');
                    const tipoB = b.querySelector('.tipo-badge');
                    valorA = tipoA ? tipoA.textContent : '';
                    valorB = tipoB ? tipoB.textContent : '';
                    break;
                default:
                    return 0;
            }

            // Comparação
            if (valorA < valorB) return direcao === 'asc' ? -1 : 1;
            if (valorA > valorB) return direcao === 'asc' ? 1 : -1;
            return 0;
        });

        // Reordenar as linhas no DOM (apenas as visíveis)
        const todasLinhas = getLinhas();
        const linhasOcultas = todasLinhas.filter(l => l.style.display === 'none');
        
        // Primeiro, adicionar todas as linhas ordenadas visíveis
        linhas.forEach(l => tabelaCorpo.appendChild(l));
        
        // Depois, adicionar as linhas ocultas no final
        linhasOcultas.forEach(l => tabelaCorpo.appendChild(l));
    }

    // Itens por página (PHP)
    const itensSelect = document.getElementById('itens-por-pagina-select');
    if (itensSelect) {
        itensSelect.addEventListener('change', function () {
            const novoLimite = this.value;
            console.log('Mudando itens por página para:', novoLimite);
            window.location.href = `?pagina=1&limite=${novoLimite}`;
        });
    }

    // Inicializar contador
    const linhasIniciais = getLinhas().length;
    contadorResultados.textContent = linhasIniciais;
    console.log('Inicializado com', linhasIniciais, 'linhas');
});