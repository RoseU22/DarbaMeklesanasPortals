document.addEventListener("DOMContentLoaded", function() {
    const cvButton = document.getElementById("cvButton");
    const languageModal = document.getElementById("languageModal");
    const closeModal = document.getElementById("closeModal");
    const confirmLanguage = document.getElementById("confirmLanguage");
    const languageSelect = document.getElementById("languageSelect");

    cvButton.addEventListener("click", function() {
        languageModal.classList.add("show");
    });

    closeModal.addEventListener("click", function() {
        languageModal.classList.remove("show");
    });

    confirmLanguage.addEventListener("click", function() {
        const selectedLanguage = languageSelect.value;
        window.location.href = "IzveidotCV.php?lang=" + selectedLanguage;
    });

    // Removed the event listener that closed the modal on clicking anywhere else
});
