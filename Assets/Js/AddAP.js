const maxAlunos = 5;
const maxProfessores = 5;

// ================== ALUNOS ==================
document.getElementById("addAluno").addEventListener("click", () => {
    const section = document.getElementById("alunos-section");
    const total = section.querySelectorAll(".aluno-input").length;

    if (total >= maxAlunos) {
        alert("Você chegou ao máximo de alunos");
        return;
    }

    const div = document.createElement("div");
    div.className = "form-group autocomplete";

    div.innerHTML = `
        <div class="autocomplete-wrapper" style="position: relative;">
            <input type="text"
                   class="autocomplete-input aluno-input"
                   autocomplete="off"
                   required>

            <input type="hidden" name="aluno[]" class="aluno-id">

            <div class="suggestions"></div>
        </div>

        <button type="button" class="btn removeAluno">Remover</button>
    `;

    section.appendChild(div);

    div.querySelector(".removeAluno").addEventListener("click", () => {
        div.remove();
    });
});

// ================== PROFESSORES ==================
document.getElementById("addProfessor").addEventListener("click", () => {
    const section = document.getElementById("professores-section");
    const total = section.querySelectorAll(".professor-input").length;

    if (total >= maxProfessores) {
        alert("Você chegou ao máximo de professores");
        return;
    }

    const div = document.createElement("div");
    div.className = "form-group autocomplete";

    div.innerHTML = `
        <div class="autocomplete-wrapper" style="position: relative;">
            <input type="text"
                   class="autocomplete-input professor-input"
                   autocomplete="off"
                   required>

            <input type="hidden" name="professor[]" class="professor-id">

            <div class="suggestions"></div>
        </div>

        <button type="button" class="btn removeProfessor">Remover</button>
    `;

    section.appendChild(div);

    div.querySelector(".removeProfessor").addEventListener("click", () => {
        div.remove();
    });
});
