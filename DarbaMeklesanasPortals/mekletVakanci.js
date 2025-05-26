document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchInput');
    const countryFilter = document.getElementById('countryFilter');
    const cityFilter = document.getElementById('cityFilter');
    const streetFilter = document.getElementById('streetFilter');
    const salaryFilter = document.getElementById('salaryFilter');

    function filterVacancies() {
        const searchValue = searchInput.value.toLowerCase();
        const selectedCountry = countryFilter.value.toLowerCase();
        const selectedCity = cityFilter.value.toLowerCase();
        const selectedStreet = streetFilter.value.toLowerCase();
        const minSalary = parseFloat(salaryFilter.value);

        const vacancies = document.querySelectorAll('.vacancy-box');

        vacancies.forEach(function (vacancy) {
            const title = vacancy.querySelector('.vacancy-title').textContent.toLowerCase();
            const locationText = vacancy.querySelector('.vacancy-location').textContent.toLowerCase();
            const salary = parseFloat(vacancy.querySelector('.openModalBtn').dataset.salary);

            const matchesTitle = title.includes(searchValue);
            const matchesCountry = selectedCountry === "" || locationText.includes(selectedCountry);
            const matchesCity = selectedCity === "" || locationText.includes(selectedCity);
            const matchesStreet = selectedStreet === "" || locationText.includes(selectedStreet);
            const matchesSalary = isNaN(minSalary) || salary >= minSalary;

            if (matchesTitle && matchesCountry && matchesCity && matchesStreet && matchesSalary) {
                vacancy.classList.remove('hidden');
            } else {
                vacancy.classList.add('hidden');
            }
        });
    }

    // Attach filter function to all inputs
    [searchInput, countryFilter, cityFilter, streetFilter, salaryFilter].forEach(input => {
        input.addEventListener('input', filterVacancies);
    });
    
    document.getElementById('resetFilters').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        document.getElementById('countryFilter').value = '';
        document.getElementById('cityFilter').value = '';
        document.getElementById('streetFilter').value = '';
        document.getElementById('salaryFilter').value = '';

        filterVacancies();

    });

});
