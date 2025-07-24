function copyShareLink(link) {
	navigator.clipboard.writeText(link).then(() => {
		Swal.fire({
			title: 'Başarılı!',
			text: 'Paylaşım linki panoya kopyalandı.',
			icon: 'success',
			timer: 2000,
			showConfirmButton: false
		});
	}).catch(() => {
		Swal.fire({
			title: 'Hata!',
			text: 'Link kopyalanamadı.',
			icon: 'error'
		});
	});
}