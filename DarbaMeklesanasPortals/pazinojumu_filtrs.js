document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.getAttribute('data-filter');

            // Remove 'active' class from all buttons
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            // Add 'active' class to clicked button
            button.classList.add('active');

            // Filter notifications
            document.querySelectorAll('.notification').forEach(note => {
                if (filter === 'all') {
                    note.style.display = 'flex';
                } else {
                    note.style.display = note.dataset.type === filter ? 'flex' : 'none';
                }
            });
        });
    });
});