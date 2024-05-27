function toggleForms() {
    const registrationForm = document.getElementById('registration-form');
    const loginForm = document.getElementById('login-form');
    if (registrationForm.classList.contains('block')) {
        registrationForm.classList.remove('block');
        registrationForm.classList.add('hidden');
        loginForm.classList.remove('hidden');
        loginForm.classList.add('block');
    } else {
        registrationForm.classList.remove('hidden');
        registrationForm.classList.add('block');
        loginForm.classList.remove('block');
        loginForm.classList.add('hidden');
    }
}