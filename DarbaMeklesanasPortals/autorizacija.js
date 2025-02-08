document.addEventListener("DOMContentLoaded", function () {
    const loginModal = document.getElementById("loginModal");
    const openLogin = document.getElementById("openLogin");
    const closeBtn = document.querySelector(".close");

    // Open Modal
    openLogin.addEventListener("click", function () {
        loginModal.classList.add("show");
    });

    // Close Modal (Only When Clicking 'X', Not Background)
    closeBtn.addEventListener("click", function () {
        loginModal.classList.remove("show");
    });

    // Prevent Closing When Clicking Inside Modal
    loginModal.querySelector(".modal-content").addEventListener("click", function (event) {
        event.stopPropagation();
    });
});
