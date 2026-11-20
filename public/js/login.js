// Fungsi untuk Toggle Show/Hide Password
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIconClosed = document.getElementById('eyeIconClosed');
    const eyeIconOpen = document.getElementById('eyeIconOpen');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIconClosed.classList.add('hidden');
        eyeIconOpen.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeIconClosed.classList.remove('hidden');
        eyeIconOpen.classList.add('hidden');
    }
}
