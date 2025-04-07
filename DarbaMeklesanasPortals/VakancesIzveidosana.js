document.addEventListener("DOMContentLoaded", function() {
    const vacancyButton = document.getElementById("vacancyButton");
    const vacancyModal = document.getElementById("vacancyModal");
    const closeVacancyModal = document.getElementById("closeVacancyModal");
    const saveVacancyButton = document.getElementById("saveVacancy");
    const vacancyNameInput = document.getElementById("vacancyName");
    const vacancyDescriptionInput = document.getElementById("vacancyDescription");
    const vacancyLocationInput = document.getElementById("vacancyLocation");
    const vacancySkillsInput = document.getElementById("vacancySkills");
    const vacancySalaryInput = document.getElementById("vacancySalary");

    let selectedVacancyId = null;

    // Atver vakances modālu
    vacancyButton.addEventListener("click", function() {
        resetVacancyForm();
        vacancyModal.classList.add("show");
    });

    // Aizver vakances modālu
    closeVacancyModal.addEventListener("click", function() {
        vacancyModal.classList.remove("show");
    });

    // Saglabā vakances datus
    saveVacancyButton.addEventListener("click", function() {
        const vacancyData = {
            id: selectedVacancyId,
            name: vacancyNameInput.value,
            description: vacancyDescriptionInput.value,
            location: vacancyLocationInput.value,
            skills: vacancySkillsInput.value,
            salary: parseFloat(vacancySalaryInput.value)
        };

        // IAzsūta datus uz php failu lai tos saglabātu datubāzē
        fetch('PHPFiles/saglabat_vakanci.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(vacancyData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Vacancy saved successfully!");
                vacancyModal.classList.remove("show");
            } else {
                alert("Error saving vacancy");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error saving vacancy");
        });
    });

    // Restartē vakances formu
    function resetVacancyForm() {
        selectedVacancyId = null;
        vacancyNameInput.value = "";
        vacancyDescriptionInput.value = "";
        vacancyLocationInput.value = "";
        vacancySkillsInput.value = "";
        vacancySalaryInput.value = "";
    }

    // Pārbaude vakances formas aizpildi pirms atļauj saglabāt
    function checkVacancyFormCompletion() {
        if (vacancyNameInput.value && vacancyDescriptionInput.value && vacancyLocationInput.value && vacancySkillsInput.value && vacancySalaryInput.value) {
            saveVacancyButton.disabled = false;
        } else {
            saveVacancyButton.disabled = true;
        }
    }

    vacancyNameInput.addEventListener("input", checkVacancyFormCompletion);
    vacancyDescriptionInput.addEventListener("input", checkVacancyFormCompletion);
    vacancyLocationInput.addEventListener("input", checkVacancyFormCompletion);
    vacancySkillsInput.addEventListener("input", checkVacancyFormCompletion);
    vacancySalaryInput.addEventListener("input", checkVacancyFormCompletion);
});
