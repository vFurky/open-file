document.addEventListener('DOMContentLoaded', function() {
	var triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs a'))
	triggerTabList.forEach(function(triggerEl) {
		var tabTrigger = new bootstrap.Tab(triggerEl)
		triggerEl.addEventListener('click', function(event) {
			event.preventDefault()
			tabTrigger.show()
		})
	})
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('settingsForm');
    const saveBtn = document.getElementById('saveBtn');
    const saveSpinner = document.getElementById('saveSpinner');

    function clearValidation() {
        form.classList.remove('was-validated');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }

    form.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid');
        });
    });

    function normalizePhone(input) {
        if (!input || input.trim() === '') return '';

        let digits = input.replace(/\D/g, '');

        if (digits.startsWith('0')) {
            digits = digits.substring(1);
        }

        if (!digits.startsWith('90')) {
            if (digits.length === 10) {
                digits = '90' + digits;
            }
        }

        if (digits.length === 12 && digits.startsWith('90')) {
            return '+' + digits;
        }

        return input;
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        clearValidation();

        const phoneInput = document.getElementById('phone');
        if (phoneInput.value.trim() !== '') {
            phoneInput.value = normalizePhone(phoneInput.value);
        }

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        saveBtn.setAttribute('disabled', 'true');
        saveSpinner.classList.remove('d-none');

        const formData = new FormData(form);
        try {
            const res  = await fetch('./profile-settings.php', { 
                method: 'POST', 
                body: formData 
            });
            const data = await res.json();

            if (data.status === 'error') {
                if (data.field) {
                    const fld = document.getElementById(data.field);
                    fld?.classList.add('is-invalid');
                    fld?.focus();
                }
                await Swal.fire({
                    icon: 'error',
                    title: 'Ups...',
                    text: data.message,
                    confirmButtonText: 'Tamam'
                });
            } else {
                await Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: data.message,
                    confirmButtonText: 'Tamam',
                    timer: 5000
                });
                window.location.reload();
            }
        } catch (err) {
            console.error(err);
            await Swal.fire({
                icon: 'error',
                title: 'Ups...',
                text: 'Beklenmedik bir hata oluştu.',
                confirmButtonText: 'Tamam'
            });
        } finally {
            saveBtn.removeAttribute('disabled');
            saveSpinner.classList.add('d-none');
        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('passwordForm');
    if (!form) return;

    const saveBtn = document.getElementById('passSaveBtn');
    const saveSpinner = document.getElementById('passSaveSpinner');
    const newPass = document.getElementById('newPassword');
    const strengthBar = document.getElementById('strengthBar');
    const strengthTxt = document.getElementById('strengthText');

    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.target);
            if (!target) return;
            target.type = target.type === 'password' ? 'text' : 'password';
            btn.textContent = target.type === 'password' ? '👁️' : '🙈';
        });
    });

    function calcStrength(pwd) {
        let score = 0;
        if (pwd.length >= 8) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[a-z]/.test(pwd)) score++;
        if (/\d/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;
        return score;
    }

    if (newPass && strengthBar && strengthTxt) {
        newPass.addEventListener('input', () => {
          const score = calcStrength(newPass.value);
          const pct = (score / 5) * 100;
          strengthBar.style.width = pct + '%';
          strengthBar.className = 'progress-bar ' +
          (pct < 40 ? 'bg-danger' : pct < 80 ? 'bg-warning' : 'bg-success');
          const labels = ['Çok zayıf', 'Zayıf', 'Orta', 'Güçlü', 'Çok güçlü'];
          strengthTxt.textContent = `Parola gücü: ${labels[Math.max(0, score-1)]}`;
      });
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();

        form.classList.remove('was-validated');
        form.querySelectorAll('.is-invalid').forEach(i => i.classList.remove('is-invalid'));

        const current = document.getElementById('currentPassword');
        const confirm = document.getElementById('confirmPassword');

        const complexity = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
        if (!complexity.test(newPass.value)) {
            newPass.classList.add('is-invalid');
            newPass.focus();
            return;
        }

        if (newPass.value !== confirm.value) {
            confirm.classList.add('is-invalid');
            confirm.focus();
            return;
        }

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        saveBtn.setAttribute('disabled', '');
        saveSpinner.classList.remove('d-none');

        const formData = new FormData(form);
        try {
            const res = await fetch('./change-password.php', { method:'POST', body:formData });
            const data = await res.json();

            if (data.status === 'error') {
                if (data.field) {
                    const fld = document.getElementById(data.field);
                    if (fld) { fld.classList.add('is-invalid'); fld.focus(); }
                }
                await Swal.fire({ 
                    icon: 'error',
                    title: 'Ups...', 
                    text: data.message, 
                    confirmButtonText: 'Tamam' 
                });
            } else {
                await Swal.fire({ 
                    icon: 'success', 
                    title: 'Başarılı!', 
                    text: data.message, 
                    confirmButtonText: 'Tamam' 
                });
                form.reset();
                window.location.reload();
            }
        } catch (err) {
            console.error(err);
            await Swal.fire({ 
                icon: 'error', 
                title: 'Ups...', 
                text: 'Sunucu hatası oluştu.', 
                confirmButtonText: 'Tamam' 
            });
        } finally {
            saveBtn.removeAttribute('disabled');
            saveSpinner.classList.add('d-none');
        }
    });
});