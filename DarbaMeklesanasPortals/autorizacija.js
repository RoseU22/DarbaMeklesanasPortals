document.addEventListener("DOMContentLoaded", function () {
    const loginModal = document.getElementById("loginModal");
    const openLoginDropdown = document.getElementById("openLoginDropdown");
    
    const LoginClientFields = document.getElementById("LoginClientFields");
    const LoginCompanyFields = document.getElementById("LoginCompanyFields");
    
    const clientFields = document.getElementById("clientFields");
    const companyFields = document.getElementById("companyFields");

    const profileBtn = document.getElementById("profileDropdownBtn");
    const profileDropdown = document.getElementById("profileDropdown");
    
    const isLoggedIn = document.getElementById("loginState").value;

    console.log(isLoggedIn);

    let selectedUserType = "klients";
    
    const loginDropdown = document.getElementById("loginDropdown");
    const dropdownOptions = document.querySelectorAll(".dropdown-option");
    const closeLoginBtn = loginModal.querySelector(".close");
    const loginForm = document.getElementById("loginForm");
    const userTypeInput = document.createElement("input"); 
    userTypeInput.type = "hidden";
    userTypeInput.name = "userType";
    userTypeInput.value = selectedUserType;
    loginForm.appendChild(userTypeInput);

    const registerModal = document.getElementById("registerModal");
    const registerLink = loginModal.querySelector(".register"); 
    const closeRegisterBtn = registerModal.querySelector(".close"); 
    const registerForm = registerModal.querySelector("#registerForm"); 

    registerLink.addEventListener("click", function () {
        loginModal.classList.remove("show");
        registerModal.classList.add("show");
    });

    closeLoginBtn.addEventListener("click", function () {
        loginModal.classList.remove("show");
        loginForm.reset();
    });

    closeRegisterBtn.addEventListener("click", function () {
        registerModal.classList.remove("show");
        registerForm.reset();
        loginForm.reset();
    });

    registerModal.querySelector(".modal-content").addEventListener("click", function (event) {
        event.stopPropagation();
    });

    dropdownOptions.forEach(option => {
        option.addEventListener("click", function () {
            
            selectedUserType = this.getAttribute("data-user-type");

            fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "userType=" + selectedUserType
            }).then(() => {
                // Paslēpj visus laukus
                document.getElementById("LoginClientFields").style.display = "none";
                document.getElementById("LoginCompanyFields").style.display = "none";
                document.getElementById("clientFields").style.display = "none";
                document.getElementById("companyFields").style.display = "none";

                // Noņem visus 'required'
                removeRequiredAttributes();

                // Parāda pareizos laukus, pamatojoties uz lietotāja konta izvēles un pievieno “required”, kur nepieciešams
                if (selectedUserType === "klients") {
                    document.getElementById("LoginClientFields").style.display = "block";
                    document.getElementById("clientFields").style.display = "block";
                    console.log(selectedUserType);
                    addRequiredAttributesToClientFields();
                } else {
                    document.getElementById("LoginCompanyFields").style.display = "block";
                    document.getElementById("companyFields").style.display = "block";
                    console.log(selectedUserType);
                    addRequiredAttributesToCompanyFields();
                }

 
                document.getElementById("loginUserType").value = selectedUserType;
                document.getElementById("registerUserType").value = selectedUserType;
            });


            document.getElementById("loginModal").classList.add("show");
        });
    });

    // Noņem visus 'required' no input laukiem
    function removeRequiredAttributes() {
        let requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.removeAttribute('required');
        });
    }

    // Pevieno 'required' klienta input laukos
    function addRequiredAttributesToClientFields() {
        document.querySelectorAll('#clientFields input').forEach(field => {
            field.setAttribute('required', true);
        });
    }

    // Pievieno 'required' uzņēmuma input laukos
    function addRequiredAttributesToCompanyFields() {
        document.querySelectorAll('#companyFields input').forEach(field => {
            field.setAttribute('required', true);
        });
    }

    if (isLoggedIn === "false") {

        openLoginDropdown.addEventListener("click", function () {
            if (loginDropdown.classList.contains("show")) {
                loginDropdown.classList.remove("show");
                setTimeout(() => loginDropdown.style.display = "none", 300); 
            } else {
                loginDropdown.style.display = "block";
                setTimeout(() => loginDropdown.classList.add("show"), 10); 
            }
        });

    } else {
        
        profileBtn.addEventListener("click", function (e) {
            e.stopPropagation(); 
            profileDropdown.classList.toggle("show"); 
        });

    }
        
    dropdownOptions.forEach(option => {
        option.addEventListener("click", function () {
            loginDropdown.classList.remove("show");
            setTimeout(() => loginDropdown.style.display = "none", 300); 
                
            document.getElementById("loginModal").classList.add("show");
        });
    });

    document.addEventListener("click", function (event) {
        if (!openLoginDropdown.contains(event.target) && !loginDropdown.contains(event.target)) {
            loginDropdown.classList.remove("show");
            setTimeout(() => loginDropdown.style.display = "none", 300);
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const profileBtn = document.getElementById("profileDropdownBtn");
        const profileDropdown = document.getElementById("profileDropdown");

        profileBtn.addEventListener("click", function (e) {
            e.stopPropagation(); 
            profileDropdown.classList.toggle("show-profile-dropdown");
        });

        document.addEventListener("click", function (e) {
            if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                profileDropdown.classList.remove("show-profile-dropdown");
            }
        });
    });

    registerModal.querySelector(".modal-content").addEventListener("click", function (event) {
        event.stopPropagation();
    });

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(loginForm);
        formData.append("userType", selectedUserType);
        console.log(selectedUserType);

        fetch("PHPFiles/login.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert("Nepareizs lietotājvārds vai parole!");
            }
        });
    });

    registerForm.addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(registerForm);
        formData.append("userType", selectedUserType);

        fetch("PHPFiles/register.php", { 
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Konts izveidots!");
                registerModal.classList.remove("show"); 
                loginModal.classList.add("show"); 
                registerForm.reset();
            } else {
                alert("Kaut kas nogāja greizi!");
            }
        });
    });
});
