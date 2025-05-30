document.addEventListener("DOMContentLoaded", function() {
const vacancyButton = document.getElementById("vacancyButton"); // Saņem pogu, kas atver vakances modālu
    const vacancyModal = document.getElementById("vacancyModal"); // Saņem vakances modāla elementu
    const closeVacancyModal = document.getElementById("closeVacancyModal"); // Saņem pogu, kas aizver vakances modālu
    const saveVacancyButton = document.getElementById("saveVacancy"); // Saņem pogu, kas saglabā vakances datus
    const vacancyNameInput = document.getElementById("vacancyName"); // Saņem vakances nosaukuma lauku
    const vacancyDescriptionInput = document.getElementById("vacancyDescription"); // Saņem vakances apraksta lauku
    const vacancySkillsInput = document.getElementById("vacancySkills"); // Saņem prasmes lauku
    const vacancySalaryInput = document.getElementById("vacancySalary"); // Saņem algas lauku
    const vacancyCountryInput = document.getElementById("vacancyCountry"); // Saņem valsts lauku
    const vacancyCityInput = document.getElementById("vacancyCity"); // Saņem pilsētas lauku
    const vacancyStreetInput = document.getElementById("vacancyStreet"); // Saņem ielas lauku

    const vacancyName = document.getElementById("vacancyName");
    const vacancyDescription = document.getElementById("vacancyDescription");
    const vacancySkills = document.getElementById("vacancySkills");
    const vacancySalary = document.getElementById("vacancySalary");
    const vacancyCountry = document.getElementById("vacancyCountry");
    const vacancyCity = document.getElementById("vacancyCity");
    const vacancyStreet = document.getElementById("vacancyStreet");

    let selectedVacancyId = null; // Mainīgais izvēlētās vakances ID glabāšanai
    let imageChanged = false;
    let originalVacancyValues = {};

    // Atver vakances modālu
    vacancyButton.addEventListener("click", function () {
        resetVacancyForm(); // pataisa formu tukšu
        document.getElementById("vacancyImageContainer").style.display='none';
        vacancyModal.classList.add("show"); // parāda modālu
    });

    // Aizver vakances modālu
    closeVacancyModal.addEventListener("click", function () {
        vacancyModal.classList.remove("show"); // paslēpj modālu
    });

    document.getElementById("vacancyImageContainer").addEventListener("click", () => {
        document.getElementById("vacancyImageInput").click();
    });

    // Priekšskata atlasīto attēlu
    document.getElementById("vacancyImageInput").addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (file) {
            document.getElementById("previewImage").src = URL.createObjectURL(file);
            imageChanged = true; // Mark image as changed
            checkVacancyFormChanges(); // Re-check to enable save button if needed
        }
    });

    // Apstrādā veidlapas iesniegšanu
    document.getElementById("saveVacancy").addEventListener("click", (e) => {
        e.preventDefault();

        const formData = new FormData(document.getElementById("vacancyForm"));

        formData.set("vakancesID", selectedVacancyId);

        fetch('PHPFiles/saglabat_vakances_attelu.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text())
        .then(data => {
            console.log(data);
        })
        .catch(error => {
            alert("Kļūda saglabājot vakanci.");
            console.error(error);
        });
    });

    // Saglabā vakances datus
    saveVacancyButton.addEventListener("click", function () {
        // Sagatavo datus no formas laukiem, ieskaitot izvēlēto vakances ID
        const vacancyData = {
            id: selectedVacancyId,
            name: vacancyNameInput.value,
            description: vacancyDescriptionInput.value,
            country: vacancyCountryInput.value,
            city: vacancyCityInput.value,
            street: vacancyStreetInput.value,
            skills: vacancySkillsInput.value,
            salary: parseFloat(vacancySalaryInput.value) // pārveido algu par skaitli
        };

        // Aizsūta datus uz php failu lai tos saglabātu datubāzē
        fetch('PHPFiles/saglabat_vakanci.php', {
            method: 'POST', // POST pieprasījums
            headers: {
                'Content-Type': 'application/json' // dati sūtīti JSON formātā
            },
            body: JSON.stringify(vacancyData) // pārvērš datus par JSON tekstu
        })
            .then(response => response.json()) // atbildi pārvērš JSON objektā
            .then(data => {
                if (data.success) {
                    alert("Vakance veiksmīgi saglabāta!"); // ja veiksmīgi, ziņo lietotājam
                    vacancyModal.classList.remove("show"); // aizver modālu
                    location.reload();
                } else {
                    alert("Saglabājot vakanci, radās kļūda"); // kļūdas gadījumā paziņo
                }
            })
            .catch(error => {
                console.error('Kļūda:', error); // izvada kļūdu konsolē
                alert("Saglabājot vakanci, radās kļūda"); // paziņo par kļūdu
            });
    });

    // Funkcija, kas pārbauda, vai visi formas lauki ir aizpildīti
    function checkVacancyFormChanges() {
        const currentValues = {
            name: vacancyNameInput.value.trim(),
            description: vacancyDescriptionInput.value.trim(),
            country: vacancyCountryInput.value.trim(),
            city: vacancyCityInput.value.trim(),
            street: vacancyStreetInput.value.trim(),
            skills: vacancySkillsInput.value.trim(),
            salary: vacancySalaryInput.value.trim()
        };

        const allFilled = Object.values(currentValues).every(val => val !== "");

        const isChanged = Object.keys(currentValues).some(key => currentValues[key] !== originalVacancyValues[key]);

        // Enable save button if all fields are filled AND either something changed OR image changed
        saveVacancyButton.disabled = !(allFilled && (isChanged || imageChanged));
    }



    // Pievieno klausītājus, lai pārbaudītu formas laukus, kad lietotājs raksta
    [
        vacancyNameInput,
        vacancyDescriptionInput,
        vacancyCountryInput,
        vacancyCityInput,
        vacancyStreetInput,
        vacancySkillsInput,
        vacancySalaryInput
    ].forEach(input => {
        input.addEventListener("input", checkVacancyFormChanges);
    });

    // Noklausās klikšķi uz vakances kastes, lai parādītu ID konsolē (testēšanai)
    document.querySelector('.vacancy-box').addEventListener('click', function (event) {
        const vacancyId = event.currentTarget.getAttribute('data-vacancy-id');
        console.log(vacancyId); // Pārbauda, vai izvada pareizo ID
    });

    // Pievieno dzēšanas pogai klikšķa notikumu katrā vakances kastē
    document.querySelectorAll(".delete-vacancy-btn").forEach(button => {
        button.addEventListener("click", function () {
            const box = this.closest(".vacancy-box");
            const vacancyId = box.getAttribute("data-vacancy-id");

            if (confirm("Vai tiešām vēlaties dzēst šo vakanci?")) {
                fetch("PHPFiles/izdzest_vakanci.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "vakancesID=" + encodeURIComponent(vacancyId)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            box.remove();
                            location.reload();
                        } else {
                            alert("Neizdevās dzēst vakanci.");
                        }
                    });
            }
        });
    });

    // Saņem vakances kastes konteineru
    const vacancyGrid = document.getElementById("vacancyGrid");

    // Klausās klikšķus uz vakances kastēm
    vacancyGrid.addEventListener("click", function (event) {

        document.getElementById("vacancyImageContainer").style.display='flex';

        if (
            event.target.closest(".delete-btn") ||
            event.target.closest(".view-stats-btn")
        ) {
            return;
        }

        const box = event.target.closest(".vacancy-box");
        if (box) {
            selectedVacancyId = event.target.closest(".vacancy-box").dataset.vacancyId;

            const previewImage = document.getElementById("previewImage");
            const imageUrl = `bilde_vakance.php?id=${selectedVacancyId}`;
            console.log("Setting preview image src to:", imageUrl);
            previewImage.src = imageUrl;

            fetchVacancyData(selectedVacancyId);
            document.querySelector("#vacancyModal h2").textContent = "Rediģēt Vakanci";
        }
    });

    // Funkcija, kas dabūd vakances datus no servera pēc ID
    function fetchVacancyData(selectedVacancyId) {
        fetch(`PHPFiles/dabut_vakanci.php?id=${selectedVacancyId}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);

                if (data.success) {
                    const vakance = data.vakance;

                    vacancyName.value = vakance.vakances_nosaukums;
                    vacancyDescription.value = vakance.vakances_apraksts;
                    vacancyCountry.value = vakance.valsts;
                    vacancyCity.value = vakance.pilseta;
                    vacancyStreet.value = vakance.iela;
                    vacancySkills.value = vakance.nepieciesamas_prasmes;
                    vacancySalary.value = vakance.maksa;

                    document.getElementById('previewImage').src = `bilde_vakance.php?id=${selectedVacancyId}`;

                    originalVacancyValues = {
                        name: vacancyName.value,
                        description: vacancyDescription.value,
                        country: vacancyCountry.value,
                        city: vacancyCity.value,
                        street: vacancyStreet.value,
                        skills: vacancySkills.value,
                        salary: vacancySalary.value
                    };

                    imageChanged = false;

                    checkVacancyFormChanges();

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
        vacancyCountryInput.value = "";
        vacancyCityInput.value = "";
        vacancyStreetInput.value = "";
        vacancySkillsInput.value = "";
        vacancySalaryInput.value = "";

        imageChanged = false;

        originalVacancyValues = {};
        checkVacancyFormChanges();
    }

    const statsModal = document.getElementById("statsModal");
    const closeStatsModal = document.getElementById("closeStatsModal");
    const statsModalContent = document.getElementById("statsModalContent");
    const statsChartCanvas = document.getElementById("statsChart");

    let myChart = null; // lai izsekotu diagrammu

    document.querySelectorAll('.view-stats-btn').forEach(button => {
        button.addEventListener('click', () => {
            const vacancyId = button.getAttribute('data-vacancy-id');

            statsModalContent.textContent = "Ielādē statistiku..."; 
            statsChartCanvas.style.display = "none";             
            statsModal.style.display = "block";                  

            fetch(`PHPFiles/dabut_vakances_statistiku.php?vacancy_id=${vacancyId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Rādīt kopējo pieteikumu skaitu
                        if (data.count === 1) {
                            statsModalContent.textContent = `Pieteikušies: ${data.count} lietotājs`;
                        } else {
                            statsModalContent.textContent = `Pieteikušies: ${data.count} lietotāji`;
                        }

                        // Ģenerē pēdējo 7 dienu datumus
                        const labels = [];
                        const values = [];
                        const dailyCounts = data.dailyCounts;

                        for (let i = 6; i >= 0; i--) {
                            const d = new Date();
                            d.setDate(d.getDate() - i);
                            const key = d.toISOString().split('T')[0]; // YYYY-MM-DD
                            labels.push(`${d.getDate()}.${d.getMonth() + 1}`); // DD.MM
                            values.push(dailyCounts[key] || 0);
                        }

                        statsModalContent.textContent = '';
                        statsChartCanvas.style.display = "block";

                        // Iznīcini veco diagrammu, ja tāda pastāv
                        if (myChart) myChart.destroy();

                        // Izveidot jaunu līniju diagrammu
                        myChart = new Chart(statsChartCanvas.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Pieteikumu skaits',
                                    data: values,
                                    fill: false,
                                    borderColor: 'var(--text-color)',     
                                    backgroundColor: 'var(--text-color)',    
                                    tension: 0.3,
                                    pointBackgroundColor: 'var(--text-color)', 
                                    pointRadius: 5,
                                    borderWidth: 2,
                                    hoverBorderColor: 'var(--accent-color)',
                                    hoverBackgroundColor: 'var(--accent-color)',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,  
                                layout: {
                                    padding: {
                                        top: 50,
                                        right: 65,
                                        bottom: 50,
                                        left: 60,
                                    }
                                },
                                plugins: {
                                    datalabels: {
                                        display: true,
                                        color: 'var(--text-color)',        
                                        align: 'top',
                                        font: { weight: 'bold', size: 12 }
                                    },
                                    tooltip: { enabled: true, mode: 'nearest' },
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        precision: 0,
                                        ticks: {
                                            stepSize: 1,
                                            color: 'var(--text-color)',   
                                            font: { size: 14 }
                                        },
                                        grid: {
                                            color: 'var(--shadow-color)'   
                                        },
                                        title: {
                                            display: true,
                                            text: 'Lietotāju skaits',
                                            color: 'var(--text-color)',
                                            font: { weight: 'bold', size: 16 }
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            color: 'var(--text-color)',    
                                            font: { size: 14 },
                                            maxRotation: 45,                
                                            minRotation: 30,
                                            autoSkip: true,
                                            maxTicksLimit: 7
                                        },
                                        grid: {
                                            color: 'var(--shadow-color)'
                                        },
                                        title: {
                                            display: true,
                                            text: 'Datums',
                                            color: 'var(--text-color)',
                                            font: { weight: 'bold', size: 16 }
                                        }
                                    }
                                }
                            },
                            plugins: [ChartDataLabels]
                        });

                    } else {
                        statsModalContent.textContent = "Statistika nav pieejama.";
                        statsChartCanvas.style.display = "none";
                    }
                })
                .catch(error => {
                    console.error('Error fetching statistics:', error);
                    statsModalContent.textContent = "Kļūda ielādējot statistiku.";
                    statsChartCanvas.style.display = "none";
                });
        });
    });

    closeStatsModal.addEventListener('click', () => {
        statsModal.style.display = "none";
        // Iznīcina diagrammu, aizverot modālu, lai atbrīvotu atmiņu
        if (myChart) {
            myChart.destroy();
            myChart = null;
        }
    });
});
