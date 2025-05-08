document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const submitButton = form.querySelector("button[type='submit']");
    const inputs = form.querySelectorAll("input");

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
        const form = this.closest("form");
    
        // Parāda priekšskatījumu
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
    
});
