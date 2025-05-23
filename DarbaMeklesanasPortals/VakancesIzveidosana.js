document.addEventListener("DOMContentLoaded", function() {
    const vacancyButton = document.getElementById("vacancyButton"); // Saņem pogu, kas atver vakances modālu
    const vacancyModal = document.getElementById("vacancyModal"); // Saņem vakances modāla elementu
    const closeVacancyModal = document.getElementById("closeVacancyModal"); // Saņem pogu, kas aizver vakances modālu
    const saveVacancyButton = document.getElementById("saveVacancy"); // Saņem pogu, kas saglabā vakances datus
    const vacancyNameInput = document.getElementById("vacancyName");
    // Saņem ievades laukus vakances datiem
    const vacancyDescriptionInput = document.getElementById("vacancyDescription");
    const vacancyLocationInput = document.getElementById("vacancyLocation");
    const vacancySkillsInput = document.getElementById("vacancySkills");
    const vacancySalaryInput = document.getElementById("vacancySalary");

    const vacancyName = document.getElementById("vacancyName");
    const vacancyDescription = document.getElementById("vacancyDescription");
    const vacancyLocation = document.getElementById("vacancyLocation");
    const vacancySkills = document.getElementById("vacancySkills");
    const vacancySalary = document.getElementById("vacancySalary");

    let selectedVacancyId = null; // Mainīgais izvēlētās vakances ID glabāšanai

    // Atver vakances modālu
    vacancyButton.addEventListener("click", function() {
        resetVacancyForm(); // pataisa formu tukšu
        vacancyModal.classList.add("show"); // parāda modālu
    });

    // Aizver vakances modālu
    closeVacancyModal.addEventListener("click", function() {
        vacancyModal.classList.remove("show"); // paslēpj modālu
    });

    // Saglabā vakances datus
    saveVacancyButton.addEventListener("click", function() {
        // Sagatavo datus no formas laukiem, ieskaitot izvēlēto vakances ID
        const vacancyData = {
            id: selectedVacancyId,
            name: vacancyNameInput.value,
            description: vacancyDescriptionInput.value,
            location: vacancyLocationInput.value,
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
    function checkVacancyFormCompletion() {
        if (
            vacancyNameInput.value.trim() && vacancyDescriptionInput.value.trim() && vacancyLocationInput.value.trim() && vacancySkillsInput.value.trim() && vacancySalaryInput.value.trim()
        ) {
            saveVacancyButton.disabled = false; // ja visi aizpildīti, poga aktivizēta
        } else {
            saveVacancyButton.disabled = true; // ja kaut kas nav, poga izslēgta
        }
    }

    // Pievieno klausītājus, lai pārbaudītu formas laukus, kad lietotājs raksta
    [vacancyNameInput, vacancyDescriptionInput, vacancyLocationInput, vacancySkillsInput, vacancySalaryInput].forEach(input => {
        input.addEventListener("input", checkVacancyFormCompletion);
    });

    // Noklausās klikšķi uz vakances kastes, lai parādītu ID konsolē (testēšanai)
    document.querySelector('.vacancy-box').addEventListener('click', function(event) {
        const vacancyId = event.currentTarget.getAttribute('data-vacancy-id');
        console.log(vacancyId); // Pārbauda, vai izvada pareizo ID
    });

    // Pievieno dzēšanas pogai klikšķa notikumu katrā vakances kastē
    document.querySelectorAll(".delete-vacancy-btn").forEach(button => {
        button.addEventListener("click", function () {
            // Atrod tuvāko vakances kasti (vecākelements)
            const box = this.closest(".vacancy-box");
            // Paņem vakances ID no datu atribūta
            const vacancyId = box.getAttribute("data-vacancy-id");

            // Apstiprinājuma logs lietotājam par dzēšanu
            if (confirm("Vai tiešām vēlaties dzēst šo vakanci?")) {
                // Nosūta POST pieprasījumu uz PHP failu dzēšanai
                fetch("PHPFiles/izdzest_vakanci.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "vakancesID=" + encodeURIComponent(vacancyId) // sūta vakances ID
                })
                .then(response => response.json()) // pārvērš atbildi JSON
                .then(data => {
                    if (data.success) {
                        box.remove(); // ja veiksmīgi, noņem vakances elementu no lapas
                        location.reload();
                    } else {
                        alert("Neizdevās dzēst vakanci."); // kļūdas gadījumā paziņo
                    }
                });
            }
        });
    });

    // Saņem vakances kastes konteineru
    const vacancyGrid = document.getElementById("vacancyGrid");

    // Klausās klikšķus uz vakances kastēm
    vacancyGrid.addEventListener("click", function(event) {

        // Ignorē abas pogas
        if (
            event.target.closest(".delete-btn") || 
            event.target.closest(".view-stats-btn")
        ) {
            return;
        }

        // Atrod tuvāko vakances kasti no klikšķa mērķa
        const box = event.target.closest(".vacancy-box");
        if (box) {
            // Iegūst vakances ID no datu atribūta
            selectedVacancyId = event.target.closest(".vacancy-box").dataset.vacancyId;
            // Dabūd vakances datus modāla aizpildīšanai
            fetchVacancyData(selectedVacancyId);
            // Maina modāla virsrakstu uz "Rediģēt Vakanci"
            document.querySelector("#vacancyModal h2").textContent = "Rediģēt Vakanci";
        }
    });
    
    // Funkcija, kas dabūd vakances datus no servera pēc ID
    function fetchVacancyData(selectedVacancyId) {
        fetch(`PHPFiles/dabut_vakanci.php?id=${selectedVacancyId}`)
            .then(response => response.json()) // pārvērš atbildi JSON
            .then(data => {

                console.log(data); // izvada datus konsolē testēšanai

                if (data.success) {
                    const vakance = data.vakance;
    
                    // Aizpildiet modālos laukus ar saņemtajiem datiem
                    vacancyName.value = vakance.vakances_nosaukums;
                    vacancyDescription.value = vakance.vakances_apraksts;
                    vacancyLocation.value = vakance.atrasanas_vieta;
                    vacancySkills.value = vakance.nepieciesamas_prasmes;
                    vacancySalary.value = vakance.maksa;
    
                    vacancyModal.classList.add("show"); // parāda modālu
                } else {
                    alert("Vakance nav atrasta."); // paziņo, ja vakance nav atrasta
                }
            })
            .catch(error => {
                console.error('Kļūda ielādējot vakances datus:', error); // kļūdas ziņa konsolē
                alert("Neizdevās iegūt vakances datus."); // paziņojums lietotājam
            });
    }


    // Restartē vakances formu
    function resetVacancyForm() {
        document.querySelector("#vacancyModal h2").textContent = "Izveidot vakanci"; // maina virsrakstu
        selectedVacancyId = null; // null - nav izvēlēta neviena vakance
        vacancyNameInput.value = ""; // notīra vakances nosaukumu
        vacancyDescriptionInput.value = ""; // notīra aprakstu
        vacancyLocationInput.value = ""; // notīra atrašanās vietu
        vacancySkillsInput.value = ""; // notīra prasmes
        vacancySalaryInput.value = ""; // notīra algu
    }

    // Pārbaude vakances formas aizpildi pirms atļauj saglabāt
    function checkVacancyFormCompletion() {
        if (vacancyNameInput.value && vacancyDescriptionInput.value && vacancyLocationInput.value && vacancySkillsInput.value && vacancySalaryInput.value) {
            saveVacancyButton.disabled = false; // ja aizpildīts - aktivizē pogu
        } else {
            saveVacancyButton.disabled = true; // ja ne - izslēdz pogu
        }
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


    // Pievieno notikumus formu laukiem, lai pārbaudītu aizpildījumu reāllaikā
    vacancyNameInput.addEventListener("input", checkVacancyFormCompletion);
    vacancyDescriptionInput.addEventListener("input", checkVacancyFormCompletion);
    vacancyLocationInput.addEventListener("input", checkVacancyFormCompletion);
    vacancySkillsInput.addEventListener("input", checkVacancyFormCompletion);
    vacancySalaryInput.addEventListener("input", checkVacancyFormCompletion);
});
