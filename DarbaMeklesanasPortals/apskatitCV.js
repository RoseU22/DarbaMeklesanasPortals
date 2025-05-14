document.addEventListener("DOMContentLoaded", function() {
    const apskatitButtons = document.querySelectorAll('.apskatit-btn');
    const cvModal = document.getElementById("cvModal");
    const closeCVModal = document.getElementById("closeCVModal");
    const saveCVButton = document.getElementById("saveCV");
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const phoneInput = document.getElementById("phone");
    const addressInput = document.getElementById("address");
    const dobInput = document.getElementById("dob");
    const educationInput = document.getElementById("education");
    const workExperienceInput = document.getElementById("workExperience");
    const skillsInput = document.getElementById("skills");
    const languagesInput = document.getElementById("languages");
    const additionalInfoInput = document.getElementById("additionalInfo");

    const translations = {
        lv: {
            cvEditTitle: "Rediģēt CV",
            cvTitle: "Izveidot savu CV",
            nameLabel: "Vārds:",
            emailLabel: "E-pasts:",
            phoneLabel: "Telefons:",
            addressLabel: "Adrese:",
            dobLabel: "Dzimšanas datums:",
            educationLabel: "Izglītība:",
            workExperienceLabel: "Darba pieredze:",
            skillsLabel: "Prasmes:",
            languagesLabel: "Valodas:",
            additionalInfoLabel: "Papildus informācija:",
            saveButton: "Saglabāt CV",
            namePlaceholder: "Ievadiet savu vārdu",
            emailPlaceholder: "Ievadiet savu e-pastu",
            phonePlaceholder: "Ievadiet savu tālruņa numuru",
            addressPlaceholder: "Ievadiet savu adresi",
            dobPlaceholder: "Izvēlieties dzimšanas datumu",
            educationPlaceholder: "Ievadiet savu izglītību",
            workExperiencePlaceholder: "Ievadiet savu darba pieredzi",
            skillsPlaceholder: "Norādiet savas prasmes",
            languagesPlaceholder: "Norādiet valodas, ko zināt",
            additionalInfoPlaceholder: "Papildus informācija"
        },
        en: {
            cvEditTitle: "Edit CV",
            cvTitle: "Create Your CV",
            nameLabel: "Name:",
            emailLabel: "Email:",
            phoneLabel: "Phone:",
            addressLabel: "Address:",
            dobLabel: "Date of Birth:",
            educationLabel: "Education:",
            workExperienceLabel: "Work Experience:",
            skillsLabel: "Skills:",
            languagesLabel: "Languages Spoken:",
            additionalInfoLabel: "Additional Information:",
            saveButton: "Save CV",
            namePlaceholder: "Enter your name",
            emailPlaceholder: "Enter your email",
            phonePlaceholder: "Enter your phone number",
            addressPlaceholder: "Enter your address",
            dobPlaceholder: "Select your date of birth",
            educationPlaceholder: "Enter your educational background",
            workExperiencePlaceholder: "Enter your work experience",
            skillsPlaceholder: "List your skills",
            languagesPlaceholder: "Enter languages you speak",
            additionalInfoPlaceholder: "Additional information"
        },
        ru: {
            cvEditTitle: "Редактировать CV",
            cvTitle: "Создайте свое резюме",
            nameLabel: "Имя:",
            emailLabel: "Электронная почта:",
            phoneLabel: "Телефон:",
            addressLabel: "Адрес:",
            dobLabel: "Дата рождения:",
            educationLabel: "Образование:",
            workExperienceLabel: "Опыт работы:",
            skillsLabel: "Навыки:",
            languagesLabel: "Языки:",
            additionalInfoLabel: "Дополнительная информация:",
            saveButton: "Сохранить резюме",
            namePlaceholder: "Введите ваше имя",
            emailPlaceholder: "Введите ваш email",
            phonePlaceholder: "Введите ваш номер телефона",
            addressPlaceholder: "Введите ваш адрес",
            dobPlaceholder: "Выберите дату рождения",
            educationPlaceholder: "Введите ваше образование",
            workExperiencePlaceholder: "Введите ваш опыт работы",
            skillsPlaceholder: "Перечислите ваши навыки",
            languagesPlaceholder: "Введите языки, которыми вы владеете",
            additionalInfoPlaceholder: "Дополнительная информация"
        }
    };


    // Iziet cauri katram apskatīt pogai
    apskatitButtons.forEach(button => {
        button.addEventListener("click", function(event) {
            const cvId = event.target.getAttribute('data-cv-id'); // Dabūd cv id
            fetchCVData(cvId); // Pasauc funkciju lai dabūtu CV datus
        });
    });

    function fetchCVData(cvId) {
        fetch(`PHPFiles/dabut_cv.php?id=${cvId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cv = data.cv;

                    const cvLanguage = data.language && translations[data.language] ? data.language : 'lv';
                    updateLanguage(cvLanguage);
    
                    nameInput.value = cv.vards;
                    emailInput.value = cv.epasts;
                    phoneInput.value = cv.talrunis;
                    addressInput.value = cv.adresse;
                    dobInput.value = cv.gads;
                    educationInput.value = cv.izglitiba;
                    workExperienceInput.value = cv.darba_pieredze;
                    skillsInput.value = cv.prasmes;
                    languagesInput.value = cv.valodas;
                    additionalInfoInput.value = cv.papildus_info;
    
                    cvModal.classList.add("show");
                } else {
                    alert("Ienesot CV datus, radās kļūda.");
                }
            })
            .catch(error => {
                console.error('Ienesot CV datus, radās kļūda.', error);
                alert("Ienesot CV datus, radās kļūda.");
            });
    }

    // Aizver modālo logu ciet
    closeCVModal.addEventListener("click", function() {
        cvModal.classList.remove("show");
    });

    function updateLanguage(language) {
        console.log('Updating language to:', language);  // redz kura valoda tiek izmantota
    
        // Atjauno modālā loga vietturus
        document.getElementById("nameLabel").textContent = translations[language].nameLabel;
        document.getElementById("emailLabel").textContent = translations[language].emailLabel;
        document.getElementById("phoneLabel").textContent = translations[language].phoneLabel;
        document.getElementById("addressLabel").textContent = translations[language].addressLabel;
        document.getElementById("dobLabel").textContent = translations[language].dobLabel;
        document.getElementById("educationLabel").textContent = translations[language].educationLabel;
        document.getElementById("workExperienceLabel").textContent = translations[language].workExperienceLabel;
        document.getElementById("skillsLabel").textContent = translations[language].skillsLabel;
        document.getElementById("languagesLabel").textContent = translations[language].languagesLabel;
        document.getElementById("additionalInfoLabel").textContent = translations[language].additionalInfoLabel;

    
        nameInput.setAttribute("placeholder", translations[language].namePlaceholder);
        emailInput.setAttribute("placeholder", translations[language].emailPlaceholder);
        phoneInput.setAttribute("placeholder", translations[language].phonePlaceholder);
        addressInput.setAttribute("placeholder", translations[language].addressPlaceholder);
        dobInput.setAttribute("placeholder", translations[language].dobPlaceholder);
        educationInput.setAttribute("placeholder", translations[language].educationPlaceholder);
        workExperienceInput.setAttribute("placeholder", translations[language].workExperiencePlaceholder);
        skillsInput.setAttribute("placeholder", translations[language].skillsPlaceholder);
        languagesInput.setAttribute("placeholder", translations[language].languagesPlaceholder);
        additionalInfoInput.setAttribute("placeholder", translations[language].additionalInfoPlaceholder);
    }

    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const pazinojumiId = this.getAttribute('data-paz-id');
            const notificationDiv = this.closest('.notification');

            fetch('PHPFiles/izdzest_pazinojumu.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `pazinojumi_id=${pazinojumiId}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    notificationDiv.remove(); // Remove the notification from the DOM
                } else {
                    alert('Neizdevās dzēst paziņojumu.');
                }
            })
            .catch(error => {
                console.error('Kļūda dzēšot paziņojumu:', error);
            });
        });
    });

});
