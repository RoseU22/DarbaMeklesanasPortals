document.addEventListener("DOMContentLoaded", () => {
    const logContainer = document.getElementById("admin-log-content");

    function animateFadeIn(element) {
        element.style.animation = 'none'; // Restartē animāciju
        void element.offsetWidth;
        element.style.animation = '';  
    }

    function loadAdminLogs(page = 1) {
        fetch('admin_log.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ page: page })
        })
        .then(response => response.text())
        .then(html => {
            logContainer.innerHTML = html;

            animateFadeIn(logContainer);


            const pagination = logContainer.querySelector('.pagination');
            if (pagination) {
                animateFadeIn(pagination);
            }


            logContainer.querySelectorAll(".pagination-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const selectedPage = btn.getAttribute("data-page");
                    loadAdminLogs(selectedPage);
                });
            });
        })
        .catch(err => {
            logContainer.innerHTML = "<p>Kļūda ielādējot žurnālu.</p>";
            console.error(err);
        });
    }

    loadAdminLogs(1);
});
