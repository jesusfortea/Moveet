// Validaciones del formulario de registro
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const dniInput = document.getElementById('dni');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const birthDateInput = document.getElementById('birth_date');

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validateDNI(dni) {
        return /^[0-9]{8}[a-zA-Z]$/.test(dni.toUpperCase());
    }

    function validatePhone(phone) {
        const cleanPhone = phone.replace(/[\s\-\+]/g, '');
        return /^[0-9]{9,}$/.test(cleanPhone);
    }

    function validatePassword(password) {
        return password.length >= 6;
    }

    function validateUsername(username) {
        return username.length >= 3 && /^[a-zA-Z0-9_]+$/.test(username);
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

    usernameInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'El nombre de usuario es requerido');
        } else if (!validateUsername(this.value)) {
            showError(this, 'Mínimo 3 caracteres, solo letras, números y guion bajo');
        } else {
            clearError(this);
        }
    });

    usernameInput.addEventListener('focus', function() {
        clearError(this);
    });

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

    dniInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'El DNI es requerido');
        } else if (!validateDNI(this.value)) {
            showError(this, 'DNI inválido (formato: 8 números + 1 letra, ej: 12345678A)');
        } else {
            clearError(this);
        }
    });

    dniInput.addEventListener('focus', function() {
        clearError(this);
    });

    phoneInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'El teléfono es requerido');
        } else if (!validatePhone(this.value)) {
            showError(this, 'Teléfono inválido (mínimo 9 dígitos)');
        } else {
            clearError(this);
        }
    });

    phoneInput.addEventListener('focus', function() {
        clearError(this);
    });

    passwordInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'La contraseña es requerida');
        } else if (!validatePassword(this.value)) {
            showError(this, 'La contraseña debe tener mínimo 6 caracteres');
        } else {
            clearError(this);
            if (confirmPasswordInput.value !== '') {
                if (this.value !== confirmPasswordInput.value) {
                    showError(confirmPasswordInput, 'Las contraseñas no coinciden');
                } else {
                    clearError(confirmPasswordInput);
                }
            }
        }
    });

    passwordInput.addEventListener('focus', function() {
        clearError(this);
    });

    confirmPasswordInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'Confirma la contraseña');
        } else if (this.value !== passwordInput.value) {
            showError(this, 'Las contraseñas no coinciden');
        } else {
            clearError(this);
        }
    });

    confirmPasswordInput.addEventListener('focus', function() {
        clearError(this);
    });

    birthDateInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'La fecha de nacimiento es requerida');
        } else {
            clearError(this);
        }
    });

    birthDateInput.addEventListener('focus', function() {
        clearError(this);
    });

    form.addEventListener('submit', function(e) {
        let isValid = true;

        if (usernameInput.value === '') {
            showError(usernameInput, 'El nombre de usuario es requerido');
            isValid = false;
        } else if (!validateUsername(usernameInput.value)) {
            showError(usernameInput, 'Mínimo 3 caracteres, solo letras, números y guion bajo');
            isValid = false;
        } else {
            clearError(usernameInput);
        }

        if (emailInput.value === '') {
            showError(emailInput, 'El correo es requerido');
            isValid = false;
        } else if (!validateEmail(emailInput.value)) {
            showError(emailInput, 'Ingresa un correo válido');
            isValid = false;
        } else {
            clearError(emailInput);
        }

        if (dniInput.value === '') {
            showError(dniInput, 'El DNI es requerido');
            isValid = false;
        } else if (!validateDNI(dniInput.value)) {
            showError(dniInput, 'DNI inválido (formato: 8 números + 1 letra, ej: 12345678A)');
            isValid = false;
        } else {
            clearError(dniInput);
        }

        if (phoneInput.value === '') {
            showError(phoneInput, 'El teléfono es requerido');
            isValid = false;
        } else if (!validatePhone(phoneInput.value)) {
            showError(phoneInput, 'Teléfono inválido (mínimo 9 dígitos)');
            isValid = false;
        } else {
            clearError(phoneInput);
        }

        if (passwordInput.value === '') {
            showError(passwordInput, 'La contraseña es requerida');
            isValid = false;
        } else if (!validatePassword(passwordInput.value)) {
            showError(passwordInput, 'La contraseña debe tener mínimo 6 caracteres');
            isValid = false;
        } else {
            clearError(passwordInput);
        }

        if (confirmPasswordInput.value === '') {
            showError(confirmPasswordInput, 'Por favor confirma la contraseña');
            isValid = false;
        } else if (confirmPasswordInput.value !== passwordInput.value) {
            showError(confirmPasswordInput, 'Las contraseñas no coinciden');
            isValid = false;
        } else {
            clearError(confirmPasswordInput);
        }

        if (birthDateInput.value === '') {
            showError(birthDateInput, 'La fecha de nacimiento es requerida');
            isValid = false;
        } else {
            clearError(birthDateInput);
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
