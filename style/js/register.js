document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('registerForm');
	const passwordInput = document.getElementById('password');
	const confirmPasswordInput = document.getElementById('confirmPassword');
	const usernameInput = document.getElementById('username');
	const emailInput = document.getElementById('email');
	const lengthRequirement = document.getElementById('length');
	const letterRequirement = document.getElementById('letter');
	const numberRequirement = document.getElementById('number');
	const specialRequirement = document.getElementById('special');

	if (form) {
		form.addEventListener('submit', function(event) {
			let isValid = true;

			if (passwordInput.value !== confirmPasswordInput.value) {
				confirmPasswordInput.setCustomValidity('Parolalar eşleşmiyor.');
				isValid = false;
			} else {
				confirmPasswordInput.setCustomValidity('');
			}

			if (!validatePassword(passwordInput.value)) {
				passwordInput.setCustomValidity('Parola, en az bir büyük ve küçük harf, bir rakam ve bir özel karakter içermeli; en az 8 karakter uzunluğunda olmalıdır.');
				isValid = false;
			} else {
				passwordInput.setCustomValidity('');
			}

			if (usernameInput.value && !usernameInput.value.match(/^[a-zA-Z0-9_]{3,20}$/)) {
				usernameInput.setCustomValidity('Kullanıcı adı yalnızca harf, rakam ve alt çizgi içerebilir; 3-20 karakter uzunluğunda olmalıdır.');
				isValid = false;
			} else {
				usernameInput.setCustomValidity('');
			}

			if (!form.checkValidity() || !isValid) {
				event.preventDefault();
				event.stopPropagation();
			}

			form.classList.add('was-validated');
		});

		passwordInput.addEventListener('input', function() {
			checkPasswordRequirements(this.value);
		});

		confirmPasswordInput.addEventListener('input', function() {
			if (this.value !== passwordInput.value) {
				this.setCustomValidity('Parolalar eşleşmiyor.');
			} else {
				this.setCustomValidity('');
			}
		});
	}

	function checkPasswordRequirements(password) {
		if (password.length >= 8) {
			lengthRequirement.classList.add('text-success');
			lengthRequirement.innerHTML = '<i class="fas fa-check"></i> En az 8 karakter,';
		} else {
			lengthRequirement.classList.remove('text-success');
			lengthRequirement.innerHTML = 'En az 8 karakter,';
		}

		const hasUpperCase = /[A-Z]/.test(password);
		const hasLowerCase = /[a-z]/.test(password);

		if (hasUpperCase && hasLowerCase) {
			letterRequirement.classList.add('text-success');
			letterRequirement.innerHTML = '<i class="fas fa-check"></i> En az 1 büyük ve küçük harf,';
		} else {
			letterRequirement.classList.remove('text-success');
			letterRequirement.innerHTML = 'En az 1 büyük ve küçük harf,';
		}

		if (/[0-9]/.test(password)) {
			numberRequirement.classList.add('text-success');
			numberRequirement.innerHTML = '<i class="fas fa-check"></i> En az 1 rakam,';
		} else {
			numberRequirement.classList.remove('text-success');
			numberRequirement.innerHTML = 'En az 1 rakam,';
		}

		if (/[^a-zA-Z0-9]/.test(password)) {
			specialRequirement.classList.add('text-success');
			specialRequirement.innerHTML = '<i class="fas fa-check"></i> En az 1 özel harf.';
		} else {
			specialRequirement.classList.remove('text-success');
			specialRequirement.innerHTML = 'En az 1 özel harf.';
		}
	}

	function validatePassword(password) {
		const minLength = password.length >= 8;
		const hasUpperCase = /[A-Z]/.test(password);
		const hasLowerCase = /[a-z]/.test(password);
		const hasNumber = /[0-9]/.test(password);
		const hasSpecial = /[^a-zA-Z0-9]/.test(password);

		return minLength && hasUpperCase && hasLowerCase && hasNumber && hasSpecial;
	}

	const togglePasswordButtons = document.querySelectorAll('.toggle-password, .toggle-confirm-password');

	togglePasswordButtons.forEach(button => {
		button.addEventListener('click', function() {
			const passwordInput = this.previousElementSibling;
			const icon = this.querySelector('i');

			if (passwordInput.type === 'password') {
				passwordInput.type = 'text';
				icon.classList.remove('fa-eye');
				icon.classList.add('fa-eye-slash');
			} else {
				passwordInput.type = 'password';
				icon.classList.remove('fa-eye-slash');
				icon.classList.add('fa-eye');
			}
		});
	});

	const googleSignupButton = document.getElementById('googleSignup');
	if (googleSignupButton) {
		googleSignupButton.addEventListener('click', function() {
			alert('Bu özellik henüz aktif değil.');
		});
	}

	const inputs = document.querySelectorAll('input');

	inputs.forEach(input => {
		if (input.type !== 'password' && input.type !== 'hidden' && input.type !== 'checkbox') {
			input.addEventListener('input', function() {
				this.value = this.value.replace(/<[^>]*>/g, '');
			});
		}
	});
});