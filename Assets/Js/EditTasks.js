document.getElementById('projeto').addEventListener('change', function() {
    const projetoId = this.value;
    const alunoSelect = document.getElementById('aluno');

    alunoSelect.innerHTML = '<option>Carregando...</option>';

    if (!projetoId) {
        alunoSelect.innerHTML = '<option>Selecione o projeto primeiro</option>';
        return;
    }

    fetch('../Config/GetAlunosProjeto.php?projeto_id=' + projetoId)
        .then(res => res.json())
        .then(data => {
            alunoSelect.innerHTML = '<option value="">Selecione um aluno</option>';

            if (data.length === 0) {
                alunoSelect.innerHTML = '<option>Nenhum aluno encontrado</option>';
                return;
            }

            data.forEach(aluno => {
                const option = document.createElement('option');
                option.value = aluno.id;
                option.textContent = aluno.nome;
                alunoSelect.appendChild(option);
            });
        })
        .catch(() => {
            alunoSelect.innerHTML = '<option>Erro ao carregar alunos</option>';
        });
});