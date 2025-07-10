document.addEventListener('DOMContentLoaded', function() {
	const passwordResetForm = document.getElementById('passwordResetForm');

	if (passwordResetForm) {
		passwordResetForm.addEventListener('submit', function(event) {
			event.preventDefault();

			if (!passwordResetForm.checkValidity()) {
				event.stopPropagation();
				passwordResetForm.classList.add('was-validated');
				return;
			}

			document.getElementById('resetForm').style.display = 'none';
			document.getElementById('emailSentContainer').style.display = 'block';
		});
	}

	const resendLink = document.getElementById('resendLink');

	if (resendLink) {
		resendLink.addEventListener('click', function(event) {
			event.preventDefault();
			// API ile E-Posta Gönderme
			// Demo için, yalnızca alert.
			alert('Şifre yenileme bağlantınız E-Posta adresinize gönderildi.');
		});
	}
});