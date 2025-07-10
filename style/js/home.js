Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function() {
	const uploadArea = new Dropzone("#fileArea", {
		url: "./upload-file.php",
		autoProcessQueue: false,
		uploadMultiple: false,
		parallelUploads: 10,
		maxFilesize: 100,
		addRemoveLinks: true,
		dictDefaultMessage: "Dosyaları sürükleyin veya seçmek için tıklayın.",
		dictFileTooBig: "Dosya çok büyük ({{filesize}}MB). Maksimum dosya boyutu: {{maxFilesize}}MB.",
		dictInvalidFileType: "Bu dosya türü yüklenemez.",
		dictResponseError: "Sunucu hatası: {{statusCode}}",
		dictCancelUpload: "Yüklemeyi iptal et",
		dictUploadCanceled: "Yükleme iptal edildi.",
		dictRemoveFile: "Kaldır",
		dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
		acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar",

		init: function() {
			const submitButton = document.querySelector("#submit-all");
			const myDropzone = this;

			submitButton.addEventListener("click", function(e) {
				e.preventDefault();
				e.stopPropagation();

				if (myDropzone.getQueuedFiles().length === 0) {
					alert("Lütfen en az bir dosya seçin.");
					return;
				}

				myDropzone.processQueue();
			});

			this.on("success", function(file, response) {
				try {
					let data = typeof response === 'string' ? JSON.parse(response) : response;

					if (data.status === 'success') {
						file.previewElement.classList.add('dz-success');

						const shareLink = `${window.location.origin}/open-file/share/${data.file.share_token}`;
						const linkElement = document.createElement('div');
						linkElement.classList.add('share-link');

						linkElement.innerHTML = `
		                <div class="mt-2">
		                    <div class="input-group">
		                        <input type="text" class="form-control form-control-sm" value="${shareLink}" readonly>
		                        <button class="btn btn-sm btn-outline-primary copy-btn" type="button">
		                            <i class="fas fa-copy"></i> Kopyala
		                        </button>
		                    </div>
		                </div>
						`;

						file.previewElement.appendChild(linkElement);

						const copyBtn = linkElement.querySelector('.copy-btn');
						copyBtn.addEventListener('click', function(e) {
							e.preventDefault();
							e.stopPropagation();
							copyToClipboard(shareLink);
						});
					} else {
						throw new Error(data.message || 'Bilinmeyen bir hata oluştu.');
					}
				} catch (e) {
					console.error('Response parsing error:', e);
					const msgElement = file.previewElement.querySelector('.dz-error-message span');
					if (msgElement) {
						msgElement.textContent = 'Sunucu yanıtı işlenemedi: ' + e.message;
					}
					file.previewElement.classList.add('dz-error');
				}
			});

			this.on("error", function(file, response) {
				let errorMessage = 'Bir hata oluştu.';

				try {
					if (typeof response === 'string' && response.trim().startsWith('{')) {
						const parsed = JSON.parse(response);
						errorMessage = parsed.message || errorMessage;
					} else if (typeof response === 'string') {
						errorMessage = response.replace(/<[^>]*>/g, '').trim() || errorMessage;
					} else if (typeof response === 'object' && response.message) {
						errorMessage = response.message;
					}
				} catch (e) {
					console.error('Error parsing response:', e);
				}

				const msgElement = file.previewElement.querySelector('.dz-error-message span');
				if (msgElement) {
					msgElement.textContent = errorMessage;
				}
			});
		}
	});
});

async function copyToClipboard(text) {
	try {
		await navigator.clipboard.writeText(text);
		showToast('Bağlantı başarıyla panoya kopyalandı!', 'success');
	} catch (err) {
		showToast('Bağlantı kopyalanamadı!', 'danger');
		console.error('Kopyalama hatası:', err);
	}
}

function showToast(message, type = 'success') {
	const toast = document.createElement('div');
	toast.classList.add('toast', 'show', `bg-${type}`, 'text-white');
	toast.style.position = 'fixed';
	toast.style.bottom = '20px';
	toast.style.right = '20px';
	toast.style.zIndex = '9999';
	toast.style.minWidth = '250px';
	toast.style.padding = '10px';
	toast.style.borderRadius = '4px';
	toast.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';

	const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';

	toast.innerHTML = `
        <div class="toast-body">
            <i class="fas fa-${icon} me-2"></i>
            ${message}
        </div>
	`;

	document.body.appendChild(toast);

	setTimeout(() => {
		toast.remove();
	}, 3000);
}