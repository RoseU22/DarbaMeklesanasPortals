document.addEventListener("DOMContentLoaded", () => {    

    const modal = document.getElementById('passwordModal');

    
    const openButtons = document.querySelectorAll('.open-password-modal-btn');

    
    const closeBtn = document.getElementById('closePasswordModal');

   
    openButtons.forEach(button => {
    button.addEventListener('click', () => {
        
        const userId = button.dataset.userid;
        
        modal.querySelector('input[name="userId"]').value = userId;

        
        modal.classList.add('show');
    });
    });

   
    closeBtn.addEventListener('click', () => {
    modal.classList.remove('show');
    });

    window.addEventListener('click', e => {
    if (e.target === modal) {
        modal.classList.remove('show');
    }
    });

});