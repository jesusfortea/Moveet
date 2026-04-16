// Validaciones del formulario de login
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validatePassword(password) {
        return password.length >= 6;
    }

    function showError(input, message) {
        input.classList.add('error');
        const errorElement = input.parentElement.querySelector('.error-message');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    function clearError(input) {
        input.classList.remove('error');
        const errorElement = input.parentElement.querySelector('.error-message');
        if (errorElement) {
            errorElement.classList.remove('show');
            errorElement.textContent = '';
        }
    }

    emailInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'El correo es requerido');
        } else if (!validateEmail(this.value)) {
            showError(this, 'Ingresa un correo válido');
        } else {
            clearError(this);
        }
    });

    emailInput.addEventListener('focus', function() {
        clearError(this);
    });

    passwordInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'La contraseña es requerida');
        } else if (!validatePassword(this.value)) {
            showError(this, 'La contraseña debe tener al menos 6 caracteres');
        } else {
            clearError(this);
        }
    });

    passwordInput.addEventListener('focus', function() {
        clearError(this);
    });

    form.addEventListener('submit', function(e) {
        let isValid = true;

        if (emailInput.value === '') {
            showError(emailInput, 'El correo es requerido');
            isValid = false;
        } else if (!validateEmail(emailInput.value)) {
            showError(emailInput, 'Ingresa un correo válido');
            isValid = false;
        } else {
            clearError(emailInput);
        }

        if (passwordInput.value === '') {
            showError(passwordInput, 'La contraseña es requerida');
            isValid = false;
        } else if (!validatePassword(passwordInput.value)) {
            showError(passwordInput, 'La contraseña debe tener al menos 6 caracteres');
            isValid = false;
        } else {
            clearError(passwordInput);
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
