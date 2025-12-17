// Essa página é para adicionar professores e alunos no form de cadastro de projetos.

const maxAlunos = 5;
const maxProfessores = 5;

// ================== ALUNOS ==================
document.getElementById("addAluno").addEventListener("click", function () {
    const alunoSection = document.getElementById("alunos-section");

    const alunosAtuais = alunoSection.querySelectorAll(".aluno-input").length;

    if (alunosAtuais < maxAlunos) {
        const newAlunoField = document.createElement("div");
        newAlunoField.classList.add("form-group", "autocomplete");

        newAlunoField.innerHTML = `
            <label>Aluno</label>
            <input 
                type="text" 
                name="aluno[]" 
                class="autocomplete-input aluno-input"
                autocomplete="off"
                required
            >
            <div class="suggestions"></div>
            <button type="button" class="btn removeAluno">Remover Aluno</button>
        `;

        alunoSection.appendChild(newAlunoField);

        newAlunoField.querySelector(".removeAluno").addEventListener("click", function () {
            alunoSection.removeChild(newAlunoField);
        });
    } else {
        alert("Você chegou ao máximo de alunos adicionados");
    }
});

// ================== PROFESSORES ==================
document.getElementById("addProfessor").addEventListener("click", function () {
    const professorSection = document.getElementById("professores-section");

    const professoresAtuais = professorSection.querySelectorAll(".professor-input").length;

    if (professoresAtuais < maxProfessores) {
        const newProfessorField = document.createElement("div");
        newProfessorField.classList.add("form-group", "autocomplete");

        newProfessorField.innerHTML = `
            <label>Professor</label>
            <input 
                type="text" 
                name="professor[]" 
                class="autocomplete-input professor-input"
                autocomplete="off"
                required
            >
            <div class="suggestions"></div>
            <button type="button" class="btn removeProfessor">Remover Professor</button>
        `;

        professorSection.appendChild(newProfessorField);

        newProfessorField.querySelector(".removeProfessor").addEventListener("click", function () {
            professorSection.removeChild(newProfessorField);
        });
    } else {
        alert("Você chegou ao máximo de professores adicionados");
    }
});
