document.addEventListener("DOMContentLoaded", function () {
    const loginModal = document.getElementById("loginModal");
    const openLoginDropdown = document.getElementById("openLoginDropdown");
    
    const LoginClientFields = document.getElementById("LoginClientFields");
    const LoginCompanyFields = document.getElementById("LoginCompanyFields");
    
    const clientFields = document.getElementById("clientFields");
    const companyFields = document.getElementById("companyFields");

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
            let selectedUserType = this.getAttribute("data-user-type");

            // Update session on the backend (via a GET/POST request)
            fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "userType=" + selectedUserType
            }).then(() => {
                // Hide all fields initially
                document.getElementById("LoginClientFields").style.display = "none";
                document.getElementById("LoginCompanyFields").style.display = "none";
                document.getElementById("clientFields").style.display = "none";
                document.getElementById("companyFields").style.display = "none";

                // Remove 'required' attribute from all fields initially
                removeRequiredAttributes();

                // Show the correct fields based on user selection and add 'required' where needed
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

                // Update hidden input values for form submission
                document.getElementById("loginUserType").value = selectedUserType;
                document.getElementById("registerUserType").value = selectedUserType;
            });

            // Open login modal
            document.getElementById("loginModal").classList.add("show");
        });
    });

    // Function to remove 'required' from all fields
    function removeRequiredAttributes() {
        let requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.removeAttribute('required');
        });
    }

    // Function to add 'required' to client fields
    function addRequiredAttributesToClientFields() {
        document.querySelectorAll('#clientFields input').forEach(field => {
            field.setAttribute('required', true);
        });
    }

    // Function to add 'required' to company fields
    function addRequiredAttributesToCompanyFields() {
        document.querySelectorAll('#companyFields input').forEach(field => {
            field.setAttribute('required', true);
        });
    }

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
        formData.append("userType", selectedUserType);
        console.log(selectedUserType)

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
