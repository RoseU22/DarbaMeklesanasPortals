document.addEventListener("DOMContentLoaded", function () {
    const loginModal = document.getElementById("loginModal");
    const openLoginDropdown = document.getElementById("openLoginDropdown");

    const LoginClientFields = document.getElementById("LoginClientFields");
    const LoginCompanyFields = document.getElementById("LoginCompanyFields");

    const clientFields = document.getElementById("clientFields");
    const companyFields = document.getElementById("companyFields");

    const profileBtn = document.getElementById("profileDropdownBtn");
    const profileDropdown = document.getElementById("profileDropdown");

    const loginForm = document.getElementById("loginForm");
    const registerModal = document.getElementById("registerModal");
    const registerForm = registerModal?.querySelector("#registerForm");

    const isLoggedIn = document.getElementById("loginState")?.value;
    console.log(isLoggedIn);

    let selectedUserType = "klients";

    const loginDropdown = document.getElementById("loginDropdown");
    const dropdownOptions = document.querySelectorAll(".dropdown-option");

    const closeLoginBtn = loginModal?.querySelector(".close");
    const registerLink = loginModal?.querySelector(".register");
    const closeRegisterBtn = registerModal?.querySelector(".close");

    const openRegister = document.getElementById("openLogin2");

    if (loginForm) {
        const userTypeInput = document.createElement("input");
        userTypeInput.type = "hidden";
        userTypeInput.name = "userType";
        userTypeInput.value = selectedUserType;
        loginForm.appendChild(userTypeInput);
    }

    if (registerLink && loginModal && registerModal) {
        registerLink.addEventListener("click", function () {
            loginModal.classList.remove("show");
            registerModal.classList.add("show");
        });
    }

    if (registerLink && loginModal && registerModal) {
        openRegister.addEventListener("click", function () {
            loginModal.classList.add("show");
            registerModal.classList.remove("show");
        });
    }

    if (closeLoginBtn && loginModal && loginForm) {
        closeLoginBtn.addEventListener("click", function () {
            loginModal.classList.remove("show");
            loginForm.reset();
        });
    }

    if (closeRegisterBtn && registerModal && registerForm && loginForm) {
        closeRegisterBtn.addEventListener("click", function () {
            registerModal.classList.remove("show");
            registerForm.reset();
            loginForm.reset();
        });
    }

    registerModal?.querySelector(".modal-content")?.addEventListener("click", function (event) {
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
                if (LoginClientFields) LoginClientFields.style.display = "none";
                if (LoginCompanyFields) LoginCompanyFields.style.display = "none";
                if (clientFields) clientFields.style.display = "none";
                if (companyFields) companyFields.style.display = "none";

                removeRequiredAttributes();

                if (selectedUserType === "klients") {
                    if (LoginClientFields) LoginClientFields.style.display = "block";
                    if (clientFields) clientFields.style.display = "block";
                    addRequiredAttributesToClientFields();
                } else {
                    if (LoginCompanyFields) LoginCompanyFields.style.display = "block";
                    if (companyFields) companyFields.style.display = "block";
                    addRequiredAttributesToCompanyFields();
                }

                const loginUserType = document.getElementById("loginUserType");
                const registerUserType = document.getElementById("registerUserType");
                if (loginUserType) loginUserType.value = selectedUserType;
                if (registerUserType) registerUserType.value = selectedUserType;
            });

            loginModal?.classList.add("show");
        });
    });

    function removeRequiredAttributes() {
        document.querySelectorAll('[required]').forEach(field => {
            field.removeAttribute('required');
        });
    }

    function addRequiredAttributesToClientFields() {
        document.querySelectorAll('#clientFields input').forEach(field => {
            field.setAttribute('required', true);
        });
    }

    function addRequiredAttributesToCompanyFields() {
        document.querySelectorAll('#companyFields input').forEach(field => {
            field.setAttribute('required', true);
        });
    }

    if (isLoggedIn === "false" && openLoginDropdown && loginDropdown) {
        openLoginDropdown.addEventListener("click", function () {
            if (loginDropdown.classList.contains("show")) {
                loginDropdown.classList.remove("show");
                setTimeout(() => loginDropdown.style.display = "none", 300);
            } else {
                loginDropdown.style.display = "block";
                setTimeout(() => loginDropdown.classList.add("show"), 10);
            }
        });

        dropdownOptions.forEach(option => {
            option.addEventListener("click", function () {
                loginDropdown.classList.remove("show");
                setTimeout(() => loginDropdown.style.display = "none", 300);
                loginModal?.classList.add("show");
            });
        });

        document.addEventListener("click", function (event) {
            if (!openLoginDropdown.contains(event.target) && !loginDropdown.contains(event.target)) {
                loginDropdown.classList.remove("show");
                setTimeout(() => loginDropdown.style.display = "none", 300);
            }
        });
    }

    if (isLoggedIn !== "false" && profileBtn && profileDropdown) {
        profileBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            profileDropdown.classList.toggle("show");
        });

        document.addEventListener("click", function (e) {
            if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                profileDropdown.classList.remove("show");
            }
        });
    }

    if (loginForm) {
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
    }

    if (registerForm) {
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
                        registerModal?.classList.remove("show");
                        loginModal?.classList.add("show");
                        registerForm.reset();
                    } else {
                        alert("Kaut kas nogāja greizi!");
                    }
                });
        });
    }
});
