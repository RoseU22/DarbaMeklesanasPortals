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

    const vacancyName = document.getElementById("vacancyName");
    const vacancyDescription = document.getElementById("vacancyDescription");
    const vacancyLocation = document.getElementById("vacancyLocation");
    const vacancySkills = document.getElementById("vacancySkills");
    const vacancySalary = document.getElementById("vacancySalary");

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

    document.querySelector('.vacancy-box').addEventListener('click', function(event) {
        const vacancyId = event.currentTarget.getAttribute('data-vacancy-id');
        console.log(vacancyId); // Check if it outputs the correct ID
    });

    const vacancyGrid = document.getElementById("vacancyGrid");

    vacancyGrid.addEventListener("click", function(event) {
        const box = event.target.closest(".vacancy-box");
        if (box) {
            selectedVacancyId = event.target.closest(".vacancy-box").dataset.vacancyId;
            fetchVacancyData(selectedVacancyId);
            document.querySelector("#vacancyModal h2").textContent = "Rediģēt Vakanci";
        }
    });
    
    function fetchVacancyData(selectedVacancyId) {
        fetch(`PHPFiles/dabut_vakanci.php?id=${selectedVacancyId}`)
            .then(response => response.json())
            .then(data => {

                console.log(data);

                if (data.success) {
                    const vakance = data.vakance;
    
                    // Fill in modal fields
                    vacancyName.value = vakance.vakances_nosaukums;
                    vacancyDescription.value = vakance.vakances_apraksts;
                    vacancyLocation.value = vakance.atrasanas_vieta;
                    vacancySkills.value = vakance.nepieciesamas_prasmes;
                    vacancySalary.value = vakance.maksa;
    
                    vacancyModal.classList.add("show");
                } else {
                    alert("Vakance nav atrasta.");
                }
            })
            .catch(error => {
                console.error('Kļūda ielādējot vakances datus:', error);
                alert("Neizdevās iegūt vakances datus.");
            });
    }


    // Restartē vakances formu
    function resetVacancyForm() {
        document.querySelector("#vacancyModal h2").textContent = "Izveidot vakanci";
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
