<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/files/assets/header.php'; ?>

<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $site_name ?> - Açık Kaynaklı, Güvenilir, Hızlı, Ücretsiz.</title>
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
						<div class="col-lg-6 text-lg-start" data-aos="fade-right">
							<h1 class="display-4 fw-bold mb-4">Hızlı ve Güvenli Dosya Paylaşım Hizmeti</h1>
							<p class="lead mb-4">Dosyalarınızı yükleyin ve istediğiniz zaman, istediğiniz yerden erişin!</p>
							<div class="d-flex gap-3 justify-content-lg-start justify-content-center">
								<a href="#upload-section" class="btn btn-primary btn-lg px-4">
									<i class="fas fa-upload me-2"></i>Dosya Yükle
								</a>
							</div>
						</div>
						<div class="col-lg-6 d-none d-lg-block" data-aos="fade-left">
							<img src="./files/images/file-upload.gif" alt="Upload File Img" class="img-fluid">
						</div>
					</div>
				</div>
				<div class="wave-bottom">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 150">
						<path fill="#ffffff" fill-opacity="1" d="M0,96L80,85.3C160,75,320,53,480,58.7C640,64,800,96,960,96C1120,96,1280,64,1360,48L1440,32L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
					</svg>
				</div>
			</header>

			<section id="upload-section" class="py-5">
				<div class="container" data-aos="fade-up">
					<div class="row justify-content-center">
						<div class="col-lg-8 text-center">
							<div class="upload-container">
								<form action="upload-file" class="dropzone" id="fileArea">
									<div class="dz-message">
										<i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
										<h4>Dosyalarınızı buraya sürükleyin</h4>
										<p>veya dosyayı seçmek için tıklayın</p>
									</div>
								</form>
								<div class="mt-4">
									<button id="submit-all" class="btn btn-primary">
										<i class="fas fa-paper-plane me-2"></i>Yükle
									</button>
								</div>
								<div class="upload-info mt-3">
									<div class="alert alert-info" role="alert">
										<i class="fas fa-info-circle me-2"></i>
										Maks. Dosya Boyutu: <strong>100MB</strong>
									</div>
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
	<script type="text/javascript" src="<?= $site_url; ?>style/js/home.js"></script>
	<script type="text/javascript" src="<?= $site_url; ?>style/js/main.js"></script>

</body>
</html>