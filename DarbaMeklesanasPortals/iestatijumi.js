document.addEventListener("DOMContentLoaded", () => {
    const settingsButton = document.getElementById("settingsButton");
    const settingsModal = document.getElementById("settingsModal");
    const toggleNewVacancies = document.getElementById("toggleNewVacancies");

    let showNewVacancies = true;  

  // Ielādē saglabāto iestatījumu no local storage
    const savedSetting = localStorage.getItem("showNewVacancies");

    if (savedSetting === null) {
        toggleNewVacancies.checked = true;
        showNewVacancies = true;
        localStorage.setItem("showNewVacancies", "true");
    } else {
        showNewVacancies = savedSetting === "true";
        toggleNewVacancies.checked = showNewVacancies;
    }

    settingsButton.addEventListener("click", () => {
        settingsModal.classList.toggle("hidden");

        if (!settingsModal.classList.contains("hidden")) {
            settingsModal.classList.add("animate");
            setTimeout(() => settingsModal.classList.remove("animate"), 300);
        }
    });

    // Izvēles rūtiņas maiņa
    toggleNewVacancies.addEventListener("change", () => {
        showNewVacancies = toggleNewVacancies.checked;
        localStorage.setItem("showNewVacancies", showNewVacancies);
        applyFilters(currentFilter);
    });

    document.addEventListener("click", (event) => {
        if (!settingsModal.contains(event.target) && !settingsButton.contains(event.target)) {
            settingsModal.classList.add("hidden");
        }
    });

    let currentFilter = "all";

    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', () => {
            currentFilter = button.getAttribute('data-filter');

            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            applyFilters(currentFilter);
        });
    });

    function applyFilters(filter) {
        let visibleCount = 0;

        document.querySelectorAll('.notification').forEach(note => {
            const type = note.dataset.type;
            const isNewVacancy = note.classList.contains('new-vacancy');

            const matchesFilter = filter === "all" || type === filter;
            const allowedBySetting = !isNewVacancy || (isNewVacancy && showNewVacancies);

            const shouldShow = matchesFilter && allowedBySetting;
            note.style.display = shouldShow ? "flex" : "none";

            if (shouldShow) visibleCount++;
        });

        const disabledMessage = document.querySelector('.disabled-message');

        if (filter === "new-vacancy" && !showNewVacancies && visibleCount === 0) {
            disabledMessage.style.display = "block";
        } else {
            disabledMessage.style.display = "none";
        }
    }

    applyFilters(currentFilter);
});
