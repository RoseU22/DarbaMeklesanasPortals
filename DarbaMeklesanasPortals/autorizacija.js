document.addEventListener("DOMContentLoaded", function () {
    const loginModal = document.getElementById("loginModal");
    const openLoginDropdown = document.getElementById("openLoginDropdown");
    
    const clientFields = document.getElementById("clientFields");
    const companyFields = document.getElementById("companyFields");

    const loginDropdown = document.getElementById("loginDropdown");
    const dropdownOptions = document.querySelectorAll(".dropdown-option");
    const closeLoginBtn = loginModal.querySelector(".close");
    const loginForm = document.getElementById("loginForm");


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
            const userType = this.getAttribute("data-user-type");
            
            if (userType === "uznemums") {
                clientFields.style.display = "none";
                companyFields.style.display = "block";
            } else {
                clientFields.style.display = "block";
                companyFields.style.display = "none";
            }
        });
    });

    openLoginDropdown.addEventListener("click", function () {
        if (loginDropdown.classList.contains("show")) {
            loginDropdown.classList.remove("show");
            setTimeout(() => loginDropdown.style.display = "none", 300); // Smooth hide
        } else {
            loginDropdown.style.display = "block";
            setTimeout(() => loginDropdown.classList.add("show"), 10); // Smooth show
        }
    });

    dropdownOptions.forEach(option => {
        option.addEventListener("click", function () {
            loginDropdown.classList.remove("show");
            setTimeout(() => loginDropdown.style.display = "none", 300); // Hide dropdown after selection
            
            document.getElementById("loginModal").classList.add("show");
        });
    });

    document.addEventListener("click", function (event) {
        if (!openLoginDropdown.contains(event.target) && !loginDropdown.contains(event.target)) {
            loginDropdown.classList.remove("show");
            setTimeout(() => loginDropdown.style.display = "none", 300);
        }
    });

    registerModal.querySelector(".modal-content").addEventListener("click", function (event) {
        event.stopPropagation();
    });

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(loginForm);

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
