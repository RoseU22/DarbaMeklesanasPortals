document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const vacancies = document.querySelectorAll('.vacancy-box');

            vacancies.forEach(function (vacancy) {
                const title = vacancy.querySelector('.vacancy-title').textContent.toLowerCase();
                vacancy.style.display = title.includes(searchValue) ? 'block' : 'none';
            });
        });
    }
});
