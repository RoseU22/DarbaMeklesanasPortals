document.addEventListener("DOMContentLoaded", function () {
    const loginModal = document.getElementById("loginModal");
    const openLogin = document.getElementById("openLogin");
    const openLogin2 = document.getElementById("openLogin2");
    const closeBtn = document.querySelector(".close");
    const loginForm = document.getElementById("loginForm");

    openLogin.addEventListener("click", function () {
        loginModal.classList.add("show");
    });

    openLogin2.addEventListener("click", function () {
        loginModal.classList.add("show");
    });

    closeBtn.addEventListener("click", function () {
        loginModal.classList.remove("show");
    });

    loginModal.querySelector(".modal-content").addEventListener("click", function (event) {
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
});
