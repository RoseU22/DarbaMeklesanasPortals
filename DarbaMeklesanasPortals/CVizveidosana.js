document.addEventListener("DOMContentLoaded", function() {
    const cvButton = document.getElementById("cvButton");
    const languageModal = document.getElementById("languageModal");
    const cvModal = document.getElementById("cvModal");
    const closeModal = document.getElementById("closeModal");
    const closeCVModal = document.getElementById("closeCVModal");
    const confirmLanguage = document.getElementById("confirmLanguage");
    const languageSelect = document.getElementById("languageSelect");
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

    cvButton.addEventListener("click", function() {
        languageModal.classList.add("show");
    });

    closeModal.addEventListener("click", function() {
        languageModal.classList.remove("show");
    });

    closeCVModal.addEventListener("click", function() {
        cvModal.classList.remove("show");
    });

    confirmLanguage.addEventListener("click", function() {
        const selectedLanguage = languageSelect.value;
        languageModal.classList.remove("show");
        console.log('Selected language: ', selectedLanguage); 

        cvModal.classList.add("show");

        document.getElementById("cvModal").querySelector("h2").textContent = translations[selectedLanguage].cvTitle;
        document.getElementById("nameLabel").textContent = translations[selectedLanguage].nameLabel;
        document.getElementById("emailLabel").textContent = translations[selectedLanguage].emailLabel;
        document.getElementById("phoneLabel").textContent = translations[selectedLanguage].phoneLabel;
        document.getElementById("addressLabel").textContent = translations[selectedLanguage].addressLabel;
        document.getElementById("dobLabel").textContent = translations[selectedLanguage].dobLabel;
        document.getElementById("educationLabel").textContent = translations[selectedLanguage].educationLabel;
        document.getElementById("workExperienceLabel").textContent = translations[selectedLanguage].workExperienceLabel;
        document.getElementById("skillsLabel").textContent = translations[selectedLanguage].skillsLabel;
        document.getElementById("languagesLabel").textContent = translations[selectedLanguage].languagesLabel;
        document.getElementById("additionalInfoLabel").textContent = translations[selectedLanguage].additionalInfoLabel;
        saveCVButton.textContent = translations[selectedLanguage].saveButton;

        nameInput.setAttribute("placeholder", translations[selectedLanguage].namePlaceholder);
        emailInput.setAttribute("placeholder", translations[selectedLanguage].emailPlaceholder);
        phoneInput.setAttribute("placeholder", translations[selectedLanguage].phonePlaceholder);
        addressInput.setAttribute("placeholder", translations[selectedLanguage].addressPlaceholder);
        dobInput.setAttribute("placeholder", translations[selectedLanguage].dobPlaceholder);
        educationInput.setAttribute("placeholder", translations[selectedLanguage].educationPlaceholder);
        workExperienceInput.setAttribute("placeholder", translations[selectedLanguage].workExperiencePlaceholder);
        skillsInput.setAttribute("placeholder", translations[selectedLanguage].skillsPlaceholder);
        languagesInput.setAttribute("placeholder", translations[selectedLanguage].languagesPlaceholder);
        additionalInfoInput.setAttribute("placeholder", translations[selectedLanguage].additionalInfoPlaceholder);
    });

    function checkCVFormCompletion() {
        if (nameInput.value && emailInput.value && phoneInput.value && addressInput.value && dobInput.value && educationInput.value && workExperienceInput.value && skillsInput.value && languagesInput.value && additionalInfoInput.value) {
            saveCVButton.disabled = false;
        } else {
            saveCVButton.disabled = true;
        }
    }

    nameInput.addEventListener("input", checkCVFormCompletion);
    emailInput.addEventListener("input", checkCVFormCompletion);
    phoneInput.addEventListener("input", checkCVFormCompletion);
    addressInput.addEventListener("input", checkCVFormCompletion);
    dobInput.addEventListener("input", checkCVFormCompletion);
    educationInput.addEventListener("input", checkCVFormCompletion);
    workExperienceInput.addEventListener("input", checkCVFormCompletion);
    skillsInput.addEventListener("input", checkCVFormCompletion);
    languagesInput.addEventListener("input", checkCVFormCompletion);
    additionalInfoInput.addEventListener("input", checkCVFormCompletion);

    const username = document.getElementById("username").value;
    console.log('Selected language: ', selectedLanguage); 

    saveCVButton.addEventListener("click", function() {
        if (nameInput.value && emailInput.value && phoneInput.value && addressInput.value && dobInput.value && educationInput.value && workExperienceInput.value && skillsInput.value && languagesInput.value && additionalInfoInput.value) {
            
            const selectedLanguage = languageSelect.value;
            
            const cvData = {
                name: nameInput.value,
                email: emailInput.value,
                phone: phoneInput.value,
                address: addressInput.value,
                dob: dobInput.value,
                education: educationInput.value,
                workExperience: workExperienceInput.value,
                skills: skillsInput.value,
                languages: languagesInput.value,
                additionalInfo: additionalInfoInput.value,
                username: username,
                language: selectedLanguage
            };
    
            console.log("Saving CV with language:", cvData.language);

            fetch('PHPFiles/saglabat_cv.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(cvData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("CV saved successfully!");
                    cvModal.classList.remove("show");
                } else {
                    alert("Error saving CV");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Error saving CV");
            });
        }
    });    
});
