// UserManagerCoord.js - Versão Corrigida
document.addEventListener('DOMContentLoaded', function() {
    console.log('Iniciando gerenciador de aprovações...');
    
    // ============================================
    // CONFIGURAÇÕES
    // ============================================
    const config = {
        itensPorPagina: 10,
        paginaAtual: 1,
        ordenacao: { coluna: '', direcao: '' }
    };
    
    // ============================================
    // ELEMENTOS DO DOM
    // ============================================
    const elementos = {
        barraBusca: document.getElementById('barra-busca'),
        filtroTipo: document.getElementById('filtro-tipo'),
        btnsRapidos: document.querySelectorAll('.filtro-btn'),
        btnLimpar: document.getElementById('limpar-busca'),
        tabelaCorpo: document.getElementById('tabela-corpo'),
        contadorResultados: document.getElementById('contador-resultados'),
        totalResultados: document.getElementById('total-resultados'),
        paginaAtualSpan: document.getElementById('pagina-atual'),
        totalPaginasSpan: document.getElementById('total-paginas'),
        itensPorPaginaSelect: document.getElementById('itens-por-pagina-select'),
        itensPorPaginaSelectBottom: document.getElementById('itens-por-pagina-select-bottom'),
        paginacaoContainer: document.getElementById('paginacao-container'),
        paginacaoBotoes: document.getElementById('paginacao-botoes'),
        paginaInfo: document.getElementById('pagina-info'),
        totalPaginasInfo: document.getElementById('total-paginas-info'),
        cabecalhosOrdenaveis: document.querySelectorAll('th[data-ordenar]')
    };
    
    // Verificar se temos dados
    if (!elementos.tabelaCorpo) {
        console.log('Tabela vazia, saindo...');
        return;
    }
    
    // ============================================
    // DADOS INICIAIS
    // ============================================
    const todasLinhas = Array.from(elementos.tabelaCorpo.querySelectorAll('tr'));
    let linhasFiltradas = [...todasLinhas];
    let linhasVisiveis = [...todasLinhas];
    
    console.log(`Total de registros: ${todasLinhas.length}`);
    
    // ============================================
    // FUNÇÕES PRINCIPAIS
    // ============================================
    
    // 1. FUNÇÃO DE FILTRAGEM (CORRIGIDA)
    function aplicarFiltros() {
        const termoBusca = elementos.barraBusca.value.toLowerCase();
        const tipoSelecionado = elementos.filtroTipo.value;
        
        console.log(`Aplicando filtros: busca="${termoBusca}", tipo="${tipoSelecionado}"`);
        
        // Filtrar linhas
        linhasFiltradas = todasLinhas.filter(linha => {
            const nomeElem = linha.querySelector('.col-nome');
            const emailElem = linha.querySelector('.col-email');
            const tipoElem = linha.querySelector('.tipo-badge');
            
            // Verificar se elementos existem
            if (!nomeElem || !emailElem || !tipoElem) {
                console.warn('Linha sem elementos necessários:', linha);
                return false;
            }
            
            const nome = nomeElem.textContent.toLowerCase();
            const email = emailElem.textContent.toLowerCase();
            const tipo = tipoElem.textContent.trim(); // IMPORTANTE: usar trim()
            
            // DEBUG: Mostrar dados da primeira linha
            if (todasLinhas.indexOf(linha) === 0) {
                console.log('Primeira linha - Tipo:', tipo, 'Comparando com:', tipoSelecionado);
            }
            
            // Filtro de busca (APENAS se houver texto na busca)
            if (termoBusca) {
                if (!nome.includes(termoBusca) && !email.includes(termoBusca)) {
                    return false;
                }
            }
            
            // Filtro por tipo (APENAS se houver tipo selecionado)
            if (tipoSelecionado) {
                if (tipo !== tipoSelecionado) {
                    return false;
                }
            }
            
            return true;
        });
        
        console.log(`Resultado filtrado: ${linhasFiltradas.length} linhas`);
        
        // DEBUG: Mostrar tipos das linhas filtradas
        if (linhasFiltradas.length > 0) {
            console.log('Tipos das linhas filtradas:', 
                linhasFiltradas.map(l => l.querySelector('.tipo-badge').textContent.trim()));
        }
        
        aplicarOrdenacao();
    }
    
    // 2. FUNÇÃO DE ORDENAÇÃO
    function aplicarOrdenacao() {
        if (!config.ordenacao.coluna) {
            // Se não há ordenação, apenas usa as linhas filtradas
            linhasVisiveis = [...linhasFiltradas];
            return;
        }
        
        const { coluna, direcao } = config.ordenacao;
        
        console.log(`Ordenando por: ${coluna} (${direcao})`);
        
        // Clonar array para ordenar
        linhasVisiveis = [...linhasFiltradas];
        
        linhasVisiveis.sort((a, b) => {
            let valorA, valorB;
            
            switch(coluna) {
                case 'nome':
                    valorA = a.querySelector('.col-nome').textContent.toLowerCase();
                    valorB = b.querySelector('.col-nome').textContent.toLowerCase();
                    break;
                case 'email':
                    valorA = a.querySelector('.col-email').textContent.toLowerCase();
                    valorB = b.querySelector('.col-email').textContent.toLowerCase();
                    break;
                case 'data':
                    valorA = new Date(a.querySelector('.col-data').dataset.data);
                    valorB = new Date(b.querySelector('.col-data').dataset.data);
                    break;
                case 'tipo':
                    valorA = a.querySelector('.tipo-badge').textContent.toLowerCase();
                    valorB = b.querySelector('.tipo-badge').textContent.toLowerCase();
                    break;
                default:
                    return 0;
            }
            
            if (valorA < valorB) return direcao === 'asc' ? -1 : 1;
            if (valorA > valorB) return direcao === 'asc' ? 1 : -1;
            return 0;
        });
    }
    
    // 3. FUNÇÃO DE PAGINAÇÃO
    function aplicarPaginacao() {
        // Se "Todos" está selecionado, mostrar tudo
        const limite = config.itensPorPagina === 9999 ? linhasVisiveis.length : config.itensPorPagina;
        const inicio = (config.paginaAtual - 1) * limite;
        const fim = inicio + limite;
        const linhasPagina = linhasVisiveis.slice(inicio, fim);
        
        // Calcular totais
        const totalLinhas = linhasVisiveis.length;
        const totalPaginas = Math.ceil(totalLinhas / (config.itensPorPagina === 9999 ? totalLinhas : config.itensPorPagina));
        
        // Ajustar página atual se necessário
        if (config.paginaAtual > totalPaginas && totalPaginas > 0) {
            config.paginaAtual = totalPaginas;
            return aplicarPaginacao(); // Recursão
        }
        
        // Limpar tabela
        elementos.tabelaCorpo.innerHTML = '';
        
        // Adicionar linhas da página atual
        linhasPagina.forEach(linha => {
            elementos.tabelaCorpo.appendChild(linha);
        });
        
        // Atualizar contadores
        elementos.contadorResultados.textContent = totalLinhas;
        elementos.totalResultados.textContent = todasLinhas.length;
        elementos.paginaAtualSpan.textContent = config.paginaAtual;
        elementos.totalPaginasSpan.textContent = totalPaginas;
        elementos.paginaInfo.textContent = config.paginaAtual;
        elementos.totalPaginasInfo.textContent = totalPaginas;
        
        // Atualizar paginação se necessário
        atualizarPaginacaoUI(totalPaginas);
        
        console.log(`Página ${config.paginaAtual}/${totalPaginas} - Mostrando ${linhasPagina.length} de ${totalLinhas} linhas`);
    }
    
    // 4. ATUALIZAR UI DA PAGINAÇÃO
    function atualizarPaginacaoUI(totalPaginas) {
        if (totalPaginas <= 1 || config.itensPorPagina === 9999) {
            elementos.paginacaoContainer.style.display = 'none';
            return;
        }
        
        elementos.paginacaoContainer.style.display = 'flex';
        elementos.paginacaoBotoes.innerHTML = '';
        
        // Botões Anterior
        if (config.paginaAtual > 1) {
            elementos.paginacaoBotoes.innerHTML += `
                <a href="#" class="pagina-btn" data-pagina="1">
                    <i class="fas fa-angle-double-left"></i>
                </a>
                <a href="#" class="pagina-btn" data-pagina="${config.paginaAtual - 1}">
                    <i class="fas fa-angle-left"></i>
                </a>
            `;
        } else {
            elementos.paginacaoBotoes.innerHTML += `
                <span class="pagina-btn disabled">
                    <i class="fas fa-angle-double-left"></i>
                </span>
                <span class="pagina-btn disabled">
                    <i class="fas fa-angle-left"></i>
                </span>
            `;
        }
        
        // Calcular páginas para mostrar
        let inicio = Math.max(1, config.paginaAtual - 2);
        let fim = Math.min(totalPaginas, config.paginaAtual + 2);
        
        // Ajustar para mostrar 5 páginas
        if (fim - inicio < 4) {
            if (config.paginaAtual <= 3) {
                fim = Math.min(5, totalPaginas);
            } else if (config.paginaAtual >= totalPaginas - 2) {
                inicio = Math.max(1, totalPaginas - 4);
            }
        }
        
        // "..." no início
        if (inicio > 1) {
            elementos.paginacaoBotoes.innerHTML += `<span class="pagina-btn">...</span>`;
        }
        
        // Números das páginas
        for (let i = inicio; i <= fim; i++) {
            const ativa = i === config.paginaAtual ? 'ativa' : '';
            elementos.paginacaoBotoes.innerHTML += `
                <a href="#" class="pagina-btn ${ativa}" data-pagina="${i}">${i}</a>
            `;
        }
        
        // "..." no final
        if (fim < totalPaginas) {
            elementos.paginacaoBotoes.innerHTML += `<span class="pagina-btn">...</span>`;
        }
        
        // Botões Próximo
        if (config.paginaAtual < totalPaginas) {
            elementos.paginacaoBotoes.innerHTML += `
                <a href="#" class="pagina-btn" data-pagina="${config.paginaAtual + 1}">
                    <i class="fas fa-angle-right"></i>
                </a>
                <a href="#" class="pagina-btn" data-pagina="${totalPaginas}">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            `;
        } else {
            elementos.paginacaoBotoes.innerHTML += `
                <span class="pagina-btn disabled">
                    <i class="fas fa-angle-right"></i>
                </span>
                <span class="pagina-btn disabled">
                    <i class="fas fa-angle-double-right"></i>
                </span>
            `;
        }
        
        // Adicionar eventos aos botões de paginação
        document.querySelectorAll('.pagina-btn[data-pagina]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                config.paginaAtual = parseInt(this.dataset.pagina);
                aplicarPaginacao();
            });
        });
    }
    
    // 5. FUNÇÃO PRINCIPAL DE ATUALIZAÇÃO
    function atualizarTabela() {
        aplicarFiltros();
        config.paginaAtual = 1; // Volta para primeira página ao filtrar
        aplicarPaginacao();
    }
    
    // ============================================
    // EVENT LISTENERS (CORRIGIDOS)
    // ============================================
    
    // BUSCA
    elementos.barraBusca.addEventListener('input', function() {
        console.log('Busca alterada:', this.value);
        atualizarTabela();
    });
    
    // FILTRO POR TIPO (SELECT) - SIMPLIFICADO
    elementos.filtroTipo.addEventListener('change', function() {
        console.log('Select alterado para:', this.value);
        
        // Atualizar botões rápidos
        elementos.btnsRapidos.forEach(btn => {
            btn.classList.remove('ativo');
            if (btn.dataset.tipo === this.value) {
                btn.classList.add('ativo');
                console.log('Ativando botão:', btn.dataset.tipo);
            }
        });
        
        atualizarTabela();
    });
    
    // FILTROS RÁPIDOS (BOTÕES) - SIMPLIFICADO
    elementos.btnsRapidos.forEach(btn => {
        btn.addEventListener('click', function() {
            const tipo = this.dataset.tipo;
            console.log('Botão rápido clicado:', tipo);
            
            // Alternar: se já está ativo, desativa
            if (this.classList.contains('ativo')) {
                elementos.filtroTipo.value = '';
                this.classList.remove('ativo');
                console.log('Desativando filtro');
            } else {
                // Ativa este e desativa outros
                elementos.filtroTipo.value = tipo;
                elementos.btnsRapidos.forEach(b => b.classList.remove('ativo'));
                this.classList.add('ativo');
                console.log('Ativando filtro:', tipo);
            }
            
            atualizarTabela();
        });
    });
    
    // LIMPAR FILTROS
    elementos.btnLimpar.addEventListener('click', function() {
        console.log('Limpando todos os filtros');
        
        elementos.barraBusca.value = '';
        elementos.filtroTipo.value = '';
        elementos.btnsRapidos.forEach(b => b.classList.remove('ativo'));
        
        // Resetar ordenação
        config.ordenacao = { coluna: '', direcao: '' };
        elementos.cabecalhosOrdenaveis.forEach(th => {
            th.dataset.direcao = '';
            const ordenacaoSpan = th.querySelector('.ordenacao');
            if (ordenacaoSpan) ordenacaoSpan.textContent = '';
        });
        
        // Resetar para mostrar todos
        linhasFiltradas = [...todasLinhas];
        linhasVisiveis = [...todasLinhas];
        config.paginaAtual = 1;
        
        aplicarPaginacao();
        
        // Atualizar contador
        elementos.contadorResultados.textContent = todasLinhas.length;
        
        elementos.barraBusca.focus();
    });
    
    // ORDENAÇÃO
    elementos.cabecalhosOrdenaveis.forEach(th => {
        th.style.cursor = 'pointer';
        
        th.addEventListener('click', function() {
            const coluna = this.dataset.ordenar;
            const direcaoAtual = this.dataset.direcao || '';
            const novaDirecao = direcaoAtual === 'asc' ? 'desc' : 'asc';
            
            // Limpar outras ordenações
            elementos.cabecalhosOrdenaveis.forEach(cabecalho => {
                cabecalho.dataset.direcao = '';
                const span = cabecalho.querySelector('.ordenacao');
                if (span) span.textContent = '';
            });
            
            // Configurar nova ordenação
            this.dataset.direcao = novaDirecao;
            const ordenacaoSpan = this.querySelector('.ordenacao');
            if (ordenacaoSpan) {
                ordenacaoSpan.textContent = novaDirecao === 'asc' ? '↑' : '↓';
            }
            
            // Aplicar ordenação
            config.ordenacao = { coluna, direcao: novaDirecao };
            aplicarOrdenacao();
            aplicarPaginacao();
        });
    });
    
    // ITENS POR PÁGINA
    function configurarSelectItens(select, outroSelect) {
        if (select) {
            select.addEventListener('change', function() {
                const valor = this.value;
                config.itensPorPagina = valor === '9999' ? 9999 : parseInt(valor);
                config.paginaAtual = 1;
                
                // Sincronizar com outro select
                if (outroSelect) {
                    outroSelect.value = valor;
                }
                
                aplicarPaginacao();
            });
        }
    }
    
    configurarSelectItens(elementos.itensPorPaginaSelect, elementos.itensPorPaginaSelectBottom);
    configurarSelectItens(elementos.itensPorPaginaSelectBottom, elementos.itensPorPaginaSelect);
    
    // ============================================
    // FUNÇÃO DE DEBUG (para testar no console)
    // ============================================
    window.debugFiltros = function() {
        console.log('=== DEBUG DOS FILTROS ===');
        console.log('Total de linhas:', todasLinhas.length);
        console.log('Filtro atual:', elementos.filtroTipo.value);
        console.log('Busca atual:', elementos.barraBusca.value);
        
        // Mostrar tipos de todas as linhas
        console.log('Tipos disponíveis:');
        const tipos = {};
        todasLinhas.forEach((linha, index) => {
            const tipoBadge = linha.querySelector('.tipo-badge');
            if (tipoBadge) {
                const tipo = tipoBadge.textContent.trim();
                tipos[tipo] = (tipos[tipo] || 0) + 1;
                
                if (index < 3) {
                    console.log(`  Linha ${index + 1}: "${tipo}"`);
                }
            }
        });
        
        console.log('Contagem por tipo:', tipos);
        
        // Testar filtro manualmente
        console.log('\nTestando filtro "Professor":');
        const professores = todasLinhas.filter(linha => {
            const tipoBadge = linha.querySelector('.tipo-badge');
            return tipoBadge && tipoBadge.textContent.trim() === 'Professor';
        });
        console.log(`Encontrados ${professores.length} professores`);
        
        console.log('\nTestando filtro "Aluno":');
        const alunos = todasLinhas.filter(linha => {
            const tipoBadge = linha.querySelector('.tipo-badge');
            return tipoBadge && tipoBadge.textContent.trim() === 'Aluno';
        });
        console.log(`Encontrados ${alunos.length} alunos`);
    };
    
    // ============================================
    // INICIALIZAÇÃO
    // ============================================
    function inicializar() {
        console.log('Inicializando sistema...');
        
        // Adicionar estilos dinâmicos
        const style = document.createElement('style');
        style.textContent = `
            .filtro-btn.ativo {
                background-color: #6c63ff !important;
                color: white !important;
                border-color: #6c63ff !important;
                box-shadow: 0 2px 5px rgba(108, 99, 255, 0.3);
            }
            
            .pagina-btn.ativa {
                background-color: #6c63ff !important;
                color: white !important;
                border-color: #6c63ff !important;
            }
            
            .pagina-btn:hover:not(.disabled) {
                background-color: #f0f0f0 !important;
            }
            
            th[data-ordenar]:hover {
                background-color: #f5f5f5 !important;
            }
            
            .ordenacao {
                margin-left: 5px;
                color: #6c63ff;
                font-weight: bold;
            }
        `;
        document.head.appendChild(style);
        
        // Executar primeira renderização
        aplicarFiltros();
        aplicarPaginacao();
        
        console.log('Sistema inicializado! Use debugFiltros() no console para testar.');
    }
    
    // Iniciar
    inicializar();
});