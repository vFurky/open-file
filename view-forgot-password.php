<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Şifremi Unuttum - <?= $site_name ?></title>
	<?php include("./files/assets/css-files.php"); ?>
    <link rel="stylesheet" href="./style/css/forgot-password.css">
</head>
<body>
	<div class="forgot-password-container">
		<div class="logo-container">
			<i class="fas fa-file"></i>
			<span class="logo-text"><?= $site_name ?></span>
		</div>
		
		<div id="resetForm">
			<h4 class="text-center mb-3">Şifreni mi unuttun?</h4>
			
			<p class="info-text">
				E-Posta adresini girerek şifre yenileme bağlantısı talep edebilirsin.
			</p>
			
			<form id="passwordResetForm" class="needs-validation" novalidate>
				<div class="mb-4">
					<label for="email" class="form-label">E-Posta</label>
					<div class="input-group">
						<span class="input-group-text"><i class="fas fa-envelope"></i></span>
						<input type="email" class="form-control" id="email" placeholder="ornek@openfile.com" required>
						<div class="invalid-feedback">
							Lütfen bir E-Posta adresi girin.
						</div>
					</div>
				</div>
				
				<div class="d-grid gap-2 mb-3">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-paper-plane me-2"></i>Bağlantı Gönder
					</button>
				</div>
				
				<div class="text-center mt-3">
					<p class="mb-0">Şifreni hatırladın mı? <a href="login" class="text-decoration-none">Giriş Yap</a>!</p>
				</div>
			</form>
		</div>
		
		<div id="emailSentContainer" class="email-sent-container">
			<div class="email-icon">
				<i class="fas fa-envelope-circle-check"></i>
			</div>
			
			<h4 class="mb-3">E-Posta adresini kontrol et!</h4>
			
			<p class="mb-4">
				Şifre yenileme bağlantını ve talimatları E-Posta adresine birkaç dakika içinde göndermiş olacağız. 
			</p>
			
			<p class="mb-3 text-muted small">
				E-Postayı almadın mı? Spam klasörünü kontrol et veya
				<a href="#" id="resendLink" class="text-decoration-none">tekrar bağlantı iste</a>.
			</p>
			
			<div class="mt-4">
				<a href="login" class="btn btn-outline-primary">
					<i class="fas fa-arrow-left me-2"></i>Girişe Dön
				</a>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="<?= $site_url; ?>style/js/forgot-password.js"></script>
</body>
</html>