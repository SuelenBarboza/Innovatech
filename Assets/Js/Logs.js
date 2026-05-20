 const rows = document.querySelectorAll('.log-row');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationBtns = document.getElementById('paginationBtns');
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const sortFilter = document.getElementById('sortFilter');
        const chips = document.querySelectorAll('.chip');
        
        const PAGE_SIZE = 25;
        let currentPage = 1;
        let filteredRows = [];
        
        function filterRows() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const role = roleFilter.value;
            const activeCat = document.querySelector('.chip.active').dataset.cat;
            
            filteredRows = Array.from(rows).filter(row => {
                const cat = row.dataset.cat;
                const rowRole = row.dataset.role;
                const searchText = row.dataset.search;
                
                if (activeCat !== 'all' && cat !== activeCat) return false;
                if (role && rowRole !== role) return false;
                if (searchTerm && !searchText.includes(searchTerm)) return false;
                
                return true;
            });
            
            const sortDir = sortFilter.value;
            filteredRows.sort((a, b) => {
                const tsA = new Date(a.dataset.ts.replace(' ', 'T'));
                const tsB = new Date(b.dataset.ts.replace(' ', 'T'));
                return sortDir === 'desc' ? tsB - tsA : tsA - tsB;
            });
            
            currentPage = 1;
            renderPage();
        }
        
        function renderPage() {
            const total = filteredRows.length;
            const totalPages = Math.ceil(total / PAGE_SIZE);
            const start = (currentPage - 1) * PAGE_SIZE;
            const end = Math.min(start + PAGE_SIZE, total);
            
            rows.forEach(row => row.style.display = 'none');
            
            for (let i = start; i < end; i++) {
                if (filteredRows[i]) filteredRows[i].style.display = 'grid';
            }
            
            if (total === 0) {
                paginationInfo.textContent = 'Nenhum resultado encontrado';
                paginationBtns.innerHTML = '';
                const emptyState = document.querySelector('.empty-state');
                if (emptyState) emptyState.style.display = 'block';
            } else {
                const emptyState = document.querySelector('.empty-state');
                if (emptyState) emptyState.style.display = 'none';
                paginationInfo.textContent = `Exibindo ${start + 1}–${end} de ${total} registros`;
                renderPaginationButtons(totalPages);
            }
        }
        
        function renderPaginationButtons(totalPages) {
            let btns = '';
            btns += `<button class="pg-btn" onclick="goPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>← Anterior</button>`;
            
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);
            
            if (startPage > 1) {
                btns += `<button class="pg-btn" onclick="goPage(1)">1</button>`;
                if (startPage > 2) btns += `<button class="pg-btn" disabled>…</button>`;
            }
            
            for (let i = startPage; i <= endPage; i++) {
                btns += `<button class="pg-btn ${i === currentPage ? 'active' : ''}" onclick="goPage(${i})">${i}</button>`;
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) btns += `<button class="pg-btn" disabled>…</button>`;
                btns += `<button class="pg-btn" onclick="goPage(${totalPages})">${totalPages}</button>`;
            }
            
            btns += `<button class="pg-btn" onclick="goPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>Próximo →</button>`;
            paginationBtns.innerHTML = btns;
        }
        
        function goPage(page) {
            const totalPages = Math.ceil(filteredRows.length / PAGE_SIZE);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderPage();
            document.querySelector('.logs-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        searchInput.addEventListener('input', () => setTimeout(filterRows, 300));
        roleFilter.addEventListener('change', filterRows);
        sortFilter.addEventListener('change', filterRows);
        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                chips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                filterRows();
            });
        });
        
        filterRows();