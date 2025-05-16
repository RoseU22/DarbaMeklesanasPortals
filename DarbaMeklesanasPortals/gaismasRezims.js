document.addEventListener("DOMContentLoaded", function () {
    const root = document.documentElement;
    const toggleButton = document.getElementById("themeToggle");
    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "light") {
        root.classList.add("light-mode");
        toggleButton.textContent = "☀️";
    } else {
        toggleButton.textContent = "🌙";
    }

    toggleButton.addEventListener("click", function () {
        root.classList.toggle("light-mode");
        const isLight = root.classList.contains("light-mode");
        toggleButton.textContent = isLight ? "☀️" : "🌙";
        localStorage.setItem("theme", isLight ? "light" : "dark");
    });

    const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
    if (!savedTheme && prefersLight) {
        root.classList.add("light-mode");
        toggleButton.textContent = "☀️";
    }

});
