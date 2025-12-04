// Essa página é para adicionar professores e alunos no form de cadastro de projetos.

const maxAlunos = 5;
const maxProfessores = 5;

document.getElementById("addAluno").addEventListener("click", function () {
    let alunoSection = document.getElementById("alunos-section");
    let alunosAtuais = alunoSection.getElementsByClassName("form-group").length;

    if (alunosAtuais < maxAlunos) {
        let newAlunoField = document.createElement("div");
        newAlunoField.classList.add("form-group");
        newAlunoField.innerHTML = `
            <label for="aluno">Aluno</label>
            <input type="text" name="aluno[]" required>
            <button type="button" class="btn removeAluno">Remover Aluno</button>
        `;
        alunoSection.appendChild(newAlunoField);

        // Adicionar funcionalidade para remover o aluno
        newAlunoField.querySelector(".removeAluno").addEventListener("click", function () {
            alunoSection.removeChild(newAlunoField);
        });
    } else {
        alert("Você chegou ao máximo de alunos adicionados");
    }
});

// Adicionar mais professores com limite
document.getElementById("addProfessor").addEventListener("click", function () {
    let professorSection = document.getElementById("professores-section");
    let professoresAtuais = professorSection.getElementsByClassName("form-group").length;

    if (professoresAtuais < maxProfessores) {
        let newProfessorField = document.createElement("div");
        newProfessorField.classList.add("form-group");
        newProfessorField.innerHTML = `
            <label for="professor">Professor</label>
            <input type="text" name="professor[]" required>
            <button type="button" class="btn removeProfessor">Remover Professor</button>
        `;
        professorSection.appendChild(newProfessorField);

        // Adicionar funcionalidade para remover o professor
        newProfessorField.querySelector(".removeProfessor").addEventListener("click", function () {
            professorSection.removeChild(newProfessorField);
        });
    } else {
        alert("Você chegou ao máximo de professores adicionados");
    }
});
