document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('resetForm');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const newPassword = document.getElementById('newPassword').value.trim();
    const confirmNewPassword = document.getElementById('confirmNewPassword').value.trim();

    if (newPassword.length < 6) {
      showMessage('Parolei jābūt vismaz 6 simbolus garai.');
      return;
    }
    if (newPassword !== confirmNewPassword) {
      showMessage('Paroles nesakrīt!');
      return;
    }

    const formData = new FormData();
    formData.append('userType', userType);
    formData.append('email', email);
    formData.append('oldPassword', '');
    formData.append('newPassword', newPassword);
    formData.append('confirmNewPassword', confirmNewPassword);
    formData.append('token', token);

    fetch('../PHPFiles/nomainit_paroli.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(text => {
      showMessage(text);
      if (text.includes('veiksmīgi')) {
        form.reset();
        setTimeout(() => {
          window.location.href = '../index.php';
        }, 3000);
      }
    })
    .catch(() => {
      showMessage('Radās kļūda, mēģiniet vēlreiz.');
    });
  });

  function showMessage(msg) {
    const messageDiv = document.getElementById('message');
    if (!messageDiv) return;
    messageDiv.textContent = msg;
    messageDiv.style.color = msg.includes('veiksmīgi') ? 'green' : 'red';
  }
});