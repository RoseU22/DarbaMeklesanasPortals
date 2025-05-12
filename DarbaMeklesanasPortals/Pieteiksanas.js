document.addEventListener("DOMContentLoaded", () => {
    const openButtons = document.querySelectorAll(".openModalBtn");
    const modal = document.getElementById("vacancyApplyModal");
    const closeBtn = modal.querySelector(".closeModalBtn");

    const title = document.getElementById("modalVacancyTitle");
    const description = document.getElementById("modalVacancyDescription");
    const location = document.getElementById("modalVacancyLocation");
    const skills = document.getElementById("modalVacancySkills");
    const salary = document.getElementById("modalVacancySalary");

    const applyButtons = document.querySelectorAll('.applyBtn');
    const cvModal = document.getElementById('cvSelectModal');
    const closeCvModal = document.querySelector('.closeCvModalBtn');
    const cvDropBox = document.querySelector('.cv-drop-box');
    const availableCvs = document.getElementById('availableCvs');
    const submitBtn = document.getElementById("submitApplicationBtn");
    const applyBtn = modal.querySelector(".applyBtn");

    let selectedVacancyId = null;
    let selectedCvElement = null;


    applyButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            selectedVacancyId = btn.getAttribute('data-vacancy-id');
            console.log("Saglabāts vakances ID:", selectedVacancyId);
            cvModal.style.display = 'block';
            availableCvs.innerHTML = "";
    
            fetch('PHPFiles/dabut_visus_cv.php')
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        availableCvs.innerHTML = "<p>Nav pieejamu CV.</p>";
                    } else {
                        data.forEach(cv => {
                            const div = document.createElement('div');
                            div.textContent = `CV Nosaukums: ${cv.vards}`;
                            div.classList.add('cv-item');
                            div.style.padding = '10px';
                            div.style.borderBottom = '1px solid #ccc';
                            div.setAttribute('draggable', 'true');
                            div.setAttribute('data-id', cv.id);
    
                            // Iesāk vilkšanu
                            div.addEventListener('dragstart', (e) => {
                                
                                e.dataTransfer.setData("text", cv.id);
    
                                // Paštaisīts vilkšanas priekšskatījums
                                const preview = document.createElement('div');
                                preview.style.position = 'absolute';
                                preview.style.top = '-1000px';
                                preview.style.left = '-1000px';
                                preview.style.padding = '10px 15px';
                                preview.style.border = '2px solid var(--accent-color)';
                                preview.style.backgroundColor = 'var(--secondary-color)';
                                preview.style.color = 'var(--text-color)';
                                preview.style.borderRadius = '8px';
                                preview.style.fontFamily = 'Poppins, sans-serif';
                                preview.style.fontSize = '14px';
                                preview.textContent = cv.vards;
                                preview.classList.add('custom-drag-preview');
                                document.body.appendChild(preview);
    
                                e.dataTransfer.setDragImage(preview, 0, 0);
                            });
    
                            // sakopšana pēc vilkšanas beigām
                            div.addEventListener('dragend', () => {
                                const previews = document.querySelectorAll('.custom-drag-preview');
                                previews.forEach(p => p.remove());
                            });
    
                            availableCvs.appendChild(div);
                        });
                    }
                });
        });
    });

    document.querySelectorAll(".openModalBtn").forEach(button => {
        button.addEventListener("click", function () {
            const vacancyId = this.getAttribute("data-id");

            // Set vacancy ID on apply button
            applyBtn.setAttribute("data-vacancy-id", vacancyId);
        });
    });
    

    function resetCvModal() {
        // Reset the drop box text
        cvDropBox.innerHTML = 'Ielikt CV';
    
        // Disable the submit button
        submitBtn.disabled = true;
    
        // Remove any previously stored CV ID
        submitBtn.removeAttribute('data-cv-id');
    }

    closeCvModal.addEventListener('click', () => {
        cvModal.style.display = 'none';
        resetCvModal();
    });

    window.addEventListener('click', (e) => {
        if (e.target === cvModal) {
            cvModal.style.display = 'none';
        }
    });

    // Drag and Drop funkcija
    function allowDrop(event) {
        event.preventDefault();
    }
    

    cvDropBox.addEventListener('dragover', allowDrop); // Atļauj vilkt pāri drop box
    cvDropBox.addEventListener('drop', drop);

    function drop(event) {
        event.preventDefault();
        const cvId = event.dataTransfer.getData("text"); // Dabūd CV ID
        selectedCvElement = cvId; //

        // Dabūd CV data balstoties pēc tā ID
        fetch(`PHPFiles/dabut_cv.php?id=${cvId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const cvName = data.cv.vards;
                    // Parāda CV vārdu iekšā drop box
                    submitBtn.disabled = false;
                    cvDropBox.innerHTML = `<div class="cv-item">${cvName}</div>`;
                } else {
                    alert("Kļūda, nenoņemt CV.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Kļūda sazinoties ar serveri.");
            });
    }

    submitBtn.addEventListener("click", () => {
        if (!selectedCvElement) {
            alert("Lūdzu, ielieciet CV lodziņā.");
            return;
        }
    
        const cvId = selectedCvElement;
        const vacancyId = selectedVacancyId;
    
        console.log("Izvēlētais CV ID:", cvId);
        console.log("Vakances ID:", vacancyId);
    
        if (!cvId || !vacancyId) {
            alert("Kļūda: nav iespējams nosūtīt pieteikumu. Trūkst CV vai vakances ID.");
            return;
        }
    
        fetch("PHPFiles/pieteikties_vakancei.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `vakance_id=${encodeURIComponent(vacancyId)}&cv_id=${encodeURIComponent(cvId)}`
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                alert("Pieteikums veiksmīgi nosūtīts!");
                cvModal.style.display = "none";
                resetCvModal();
            } else {
                alert("Kļūda: " + response.message);
            }
        })
        .catch(error => {
            console.error("Kļūda pieprasījumā:", error);
            alert("Kļūda nosūtot datus uz serveri.");
        });
    });
    
    

    openButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            title.textContent = btn.dataset.title;
            description.textContent = btn.dataset.description;
            location.textContent = btn.dataset.location;
            skills.textContent = btn.dataset.skills;
            salary.textContent = btn.dataset.salary;

            modal.classList.add("active");
        });
    });

    closeBtn.addEventListener("click", () => {
        modal.classList.remove("active");
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.remove("active");
        }
    });
});
