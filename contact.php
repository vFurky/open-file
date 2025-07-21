<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/files/assets/header.php'; ?>

<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>İletişim - <?= $site_name ?></title>
	<link rel="stylesheet" href="./style/css/main.css">
	<?php include("./files/assets/css-files.php"); ?>
</head>
<body>
	<div class="wrapper">
		<?php include("./files/assets/navbar.php"); ?>

		<div class="content">
			<header class="hero-section text-white text-center">
				<div class="container">
					<div class="row align-items-center">
						<div class="col-lg-8 mx-auto" data-aos="fade-up">
							<h1 class="display-4 fw-bold mb-4">İletişim</h1>
							<p class="lead mb-4">Sorularınız, önerileriniz veya geri bildirimleriniz için bize ulaşın!</p>
						</div>
					</div>
				</div>
				<div class="wave-bottom">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 150">
						<path fill="#ffffff" fill-opacity="1" d="M0,96L80,85.3C160,75,320,53,480,58.7C640,64,800,96,960,96C1120,96,1280,64,1360,48L1440,32L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
					</svg>
				</div>
			</header>

			<section class="py-5">
				<div class="container" data-aos="fade-up">
					<div class="row g-5">
						<div class="col-lg-5">
							<div class="card border-0 shadow-sm h-100">
								<div class="card-body p-4">
									<h3 class="mb-4">İletişim Bilgilerimiz</h3>
									
									<div class="d-flex mb-4">
										<div class="flex-shrink-0">
											<i class="fas fa-map-marker-alt fa-2x text-primary"></i>
										</div>
										<div class="ms-3">
											<h5>Adres</h5>
											<p class="mb-0">İstanbul, Türkiye</p>
										</div>
									</div>
									
									<div class="d-flex mb-4">
										<div class="flex-shrink-0">
											<i class="fas fa-envelope fa-2x text-primary"></i>
										</div>
										<div class="ms-3">
											<h5>E-Posta</h5>
											<p class="mb-0">info@openfile.org</p>
										</div>
									</div>
									
									<div class="d-flex mb-4">
										<div class="flex-shrink-0">
											<i class="fas fa-phone fa-2x text-primary"></i>
										</div>
										<div class="ms-3">
											<h5>Telefon</h5>
											<p class="mb-0">0332 000 00 00</p>
										</div>
									</div>
									
									<div class="mt-5">
										<h5 class="mb-3">Bizi Takip Edin</h5>
										<div class="social-links d-flex gap-3">
											<a href="#" class="btn btn-outline-primary rounded-circle">
												<i class="fab fa-github"></i>
											</a>
											<a href="#" class="btn btn-outline-primary rounded-circle">
												<i class="fab fa-twitter"></i>
											</a>
											<a href="#" class="btn btn-outline-primary rounded-circle">
												<i class="fab fa-linkedin-in"></i>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-lg-7">
							<div class="card border-0 shadow-sm">
								<div class="card-body p-4">
									<h3 class="mb-4">Mesaj Gönderin</h3>
									
									<form id="contactForm">
										<div class="row g-4">
											<div class="col-md-6">
												<div class="form-floating">
													<input type="text" class="form-control" id="name" placeholder="İsim ve Soyisminiz" required>
													<label for="name">İsim ve Soyisminiz</label>
												</div>
											</div>
											
											<div class="col-md-6">
												<div class="form-floating">
													<input type="email" class="form-control" id="email" placeholder="E-Posta Adresiniz" required>
													<label for="email">E-Posta Adresiniz</label>
												</div>
											</div>
											
											<div class="col-12">
												<div class="form-floating">
													<input type="text" class="form-control" id="subject" placeholder="Konu Başlığı" required>
													<label for="subject">Konu Başlığı</label>
												</div>
											</div>
											
											<div class="col-12">
												<div class="form-floating">
													<textarea class="form-control" id="message" placeholder="Mesajınız" style="height: 150px" required></textarea>
													<label for="message">Mesajınız</label>
												</div>
											</div>
											
											<div class="col-12 mt-3">
												<button type="submit" class="btn btn-primary btn-lg px-4">
													<i class="fas fa-paper-plane me-2"></i>Mesaj Gönder
												</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>

		<?php include("./files/assets/footer.php"); ?>
	</div>

	<?php include("./files/assets/js-files.php"); ?>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
	<script type="text/javascript" src="<?= $site_url; ?>style/js/contact.js"></script>
	<script type="text/javascript" src="<?= $site_url; ?>style/js/main.js"></script>

</body>
</html>