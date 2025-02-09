document.addEventListener("DOMContentLoaded", function () {
    // Login Modal
    const loginModal = document.getElementById("loginModal");
    const openLogin = document.getElementById("openLogin");
    const openLogin2 = document.getElementById("openLogin2");
    const closeBtn = document.querySelector(".close");
    const loginForm = document.getElementById("loginForm");

    // Registration Modal
    const registerModal = document.getElementById("registerModal");
    const registerLink = loginModal.querySelector(".register"); // Register link inside login modal
    const closeRegisterBtn = registerModal.querySelector(".close"); // Close button for the register modal
    const registerForm = registerModal.querySelector("#registerForm"); // Register form

    // Open login modal
    openLogin.addEventListener("click", function () {
        loginModal.classList.add("show");
    });

    openLogin2.addEventListener("click", function () {
        loginModal.classList.add("show");
    });

    // Close login modal
    closeBtn.addEventListener("click", function () {
        loginModal.classList.remove("show");
    });

    // Stop click event from propagating when clicking inside modal content
    loginModal.querySelector(".modal-content").addEventListener("click", function (event) {
        event.stopPropagation();
    });

    // Open register modal from the login modal
    registerLink.addEventListener("click", function () {
        loginModal.classList.remove("show");
        registerModal.classList.add("show");
    });

    // Close register modal
    closeRegisterBtn.addEventListener("click", function () {
        registerModal.classList.remove("show");
        registerForm.reset();
    });

    // Stop click event from propagating when clicking inside the register modal content
    registerModal.querySelector(".modal-content").addEventListener("click", function (event) {
        event.stopPropagation();
    });

    // Handle login form submission
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

    // Handle register form submission
    registerForm.addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(registerForm);

        fetch("PHPFiles/register.php", { // Assuming the register functionality is handled by register.php
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Konts izveidots!");
                registerModal.classList.remove("show"); // Close the register modal after successful registration
                loginModal.classList.add("show"); // Optionally, open the login modal
                registerForm.reset();
            } else {
                alert("Kaut kas nogāja greizi!");
            }
        });
    });
});
