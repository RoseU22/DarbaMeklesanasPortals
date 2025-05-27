document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.getAttribute('data-filter');

            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

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