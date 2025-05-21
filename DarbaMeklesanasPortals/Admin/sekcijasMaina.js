document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Get target section ID from data attribute
        const targetId = link.getAttribute('data-target');

        // Hide all sections
        document.querySelectorAll('.section').forEach(section => {
        section.style.display = 'none';
        });

        // Show the selected section
        const targetSection = document.getElementById(targetId);
        if(targetSection) {
        targetSection.style.display = 'block';
        }
    });
    });
});
