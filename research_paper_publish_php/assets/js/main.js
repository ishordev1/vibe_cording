// Dashboard sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.value) && this.value !== '') {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    });
    
    // Password strength indicator
    const passwordInput = document.querySelector('input[name="password"]');
    if (passwordInput) {
        const strengthBar = document.createElement('div');
        strengthBar.className = 'password-strength mt-2';
        strengthBar.innerHTML = '<div class="strength-bar"></div><small class="strength-text"></small>';
        passwordInput.parentNode.appendChild(strengthBar);
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const bar = strengthBar.querySelector('.strength-bar');
            const text = strengthBar.querySelector('.strength-text');
            
            bar.style.width = (strength * 25) + '%';
            
            if (strength === 0) {
                bar.style.background = '#e2e8f0';
                text.textContent = '';
            } else if (strength <= 2) {
                bar.style.background = '#ef4444';
                text.textContent = 'Weak password';
            } else if (strength === 3) {
                bar.style.background = '#f59e0b';
                text.textContent = 'Medium password';
            } else {
                bar.style.background = '#10b981';
                text.textContent = 'Strong password';
            }
        });
    }
});

// Add strength bar CSS dynamically
const style = document.createElement('style');
style.textContent = `
    .password-strength {
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .strength-bar {
        height: 100%;
        width: 0%;
        transition: all 0.3s;
        background: #e2e8f0;
    }
    .strength-text {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.75rem;
    }
`;
document.head.appendChild(style);
