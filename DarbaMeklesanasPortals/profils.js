document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const submitButton = form.querySelector("button[type='submit']");
    const inputs = form.querySelectorAll("input");

    // Store the initial values of all inputs
    const initialValues = {};
    inputs.forEach(input => {
        initialValues[input.name] = input.value;
    });

    // Disable the button at the start
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
});
