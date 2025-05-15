document.addEventListener("DOMContentLoaded", () => {
    const deleteBtn = document.querySelector(".delete-account-btn");
    const modalOverlay = document.querySelector(".modal-overlay");
    const cancelBtn = document.querySelector(".cancel-delete");
    const confirmBtn = document.querySelector(".confirm-delete");

    deleteBtn.addEventListener("click", () => {
        modalOverlay.style.display = "flex";
    });

    cancelBtn.addEventListener("click", () => {
        modalOverlay.style.display = "none";
    });

    confirmBtn.addEventListener("click", () => {
        const username = document.getElementById("confirm-username").value.trim();
        const email = document.getElementById("confirm-email").value.trim();
        const password = document.getElementById("confirm-password").value.trim();

        if (username === "" || email === "" || password === "") {
            alert("Lūdzu, aizpildiet visus laukus.");
            return;
        }

        if (confirm("Vai tiešām vēlies dzēst profilu?")) {
            fetch("PHPFiles/izdzest_profilu.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    alert("Profils ir veiksmīgi dzēsts.");
                    window.location.href = "index.php";
                } else {
                    alert("Kļūda: " + data);
                }
            })
            .catch(err => {
                alert("Servera kļūda.");
                console.error(err);
            });
        }
    });
});
