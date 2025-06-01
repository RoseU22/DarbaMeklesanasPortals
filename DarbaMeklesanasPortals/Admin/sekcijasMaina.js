document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        
        
        const targetId = link.getAttribute('data-target');

       
        document.querySelectorAll('.section').forEach(section => {
        section.style.display = 'none';
        });

       
        const targetSection = document.getElementById(targetId);
        if(targetSection) {
        targetSection.style.display = 'block';
        }
    });
    });
});
