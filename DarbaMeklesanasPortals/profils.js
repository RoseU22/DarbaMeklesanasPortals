document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const submitButton = form.querySelector("button[type='submit']");
    const inputs = form.querySelectorAll("input");
    const modal = document.querySelector(".confirm-password-modal");
    const confirmBtn = document.getElementById('confirmPasswordBtn');
    const closeBtn = document.querySelector('.confirm-password-modal .close');
    const passwordError = document.getElementById("passwordError");

    const initialValues = {};
    inputs.forEach(input => {
        initialValues[input.name] = input.value;
    });

    submitButton.disabled = true;

    form.addEventListener("input", function () {
        let changed = false;
        inputs.forEach(input => {
            if (input.value !== initialValues[input.name]) {
                changed = true;
            }
        });
        submitButton.disabled = !changed;
    });

    document.getElementById("profile-image").addEventListener("change", function () {
        const preview = document.getElementById("profile-image-preview");
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                preview.src = reader.result;
            };
            reader.readAsDataURL(file);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        modal.style.display = 'flex';
    });

    closeBtn.onclick = () => {
        modal.style.display = 'none';
        passwordError.textContent = '';
        document.getElementById('confirmPasswordInput').value = '';
    };

    confirmBtn.onclick = async () => {
        const passwordInput = document.getElementById('confirmPasswordInput');
        const password = passwordInput.value.trim();

        if (!password) {
            passwordError.textContent = "Lūdzu, ievadiet paroli!";
            return;
        }

        
        const formData = new FormData(form);
        formData.append("confirm_password", password);

        try {
            const response = await fetch("profils.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = "profils.php";
            } else {
                passwordError.textContent = result.message || "Radās kļūda.";
                modal.style.display = "flex";
            }

        } catch (error) {
            window.location.href = "profils.php";
        }
    };
});
