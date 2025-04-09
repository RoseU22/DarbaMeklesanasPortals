document.addEventListener("DOMContentLoaded", () => {
    const openButtons = document.querySelectorAll(".openModalBtn");
    const modal = document.getElementById("vacancyApplyModal");
    const closeBtn = modal.querySelector(".closeModalBtn");

    const title = document.getElementById("modalVacancyTitle");
    const description = document.getElementById("modalVacancyDescription");
    const location = document.getElementById("modalVacancyLocation");
    const skills = document.getElementById("modalVacancySkills");
    const salary = document.getElementById("modalVacancySalary");

    openButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            title.textContent = btn.dataset.title;
            description.textContent = btn.dataset.description;
            location.textContent = btn.dataset.location;
            skills.textContent = btn.dataset.skills;
            salary.textContent = btn.dataset.salary;

            modal.classList.add("active");
        });
    });

    closeBtn.addEventListener("click", () => {
        modal.classList.remove("active");
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.remove("active");
        }
    });
});
