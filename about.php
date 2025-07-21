<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/files/assets/header.php'; ?>

<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Hakkımızda - <?= $site_name ?></title>
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
							<h1 class="display-4 fw-bold mb-4">Hakkımızda</h1>
							<p class="lead mb-4">OpenFile ekibi olarak açık kaynak, güvenilir ve hızlı dosya paylaşım hizmeti sunmak için buradayız.</p>
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
					<div class="row justify-content-center">
						<div class="col-lg-8">
							<div class="card shadow-sm border-0 rounded-lg p-4 mb-5">
								<div class="card-body">
									<h2 class="h3 mb-4 text-center">Misyonumuz</h2>
									<p>OpenFile olarak, kullanıcılarımıza açık kaynak, güvenilir, hızlı ve ücretsiz bir dosya paylaşım hizmeti sunmayı hedefliyoruz. İnternet üzerinden dosya paylaşımını herkes için erişilebilir ve kolay hale getirmeyi amaçlıyoruz.</p>
									<p>Dosyalarınızı güvenle saklayabilmeniz ve istediğiniz zaman, istediğiniz yerden erişebilmeniz için modern teknolojiler kullanarak geliştirdiğimiz platformumuz, hızlı, güvenli ve kullanıcı dostu bir deneyim sunmaktadır.</p>
								</div>
							</div>
							
							<div class="row mb-5" data-aos="fade-up" data-aos-delay="100">
								<div class="col-md-6 mb-4 mb-md-0">
									<div class="card h-100 border-0 shadow-sm rounded-lg">
										<div class="card-body text-center p-4">
											<div class="mb-3">
												<i class="fas fa-shield-alt text-primary fa-3x"></i>
											</div>
											<h3 class="h4 mb-3">Güvenlik</h3>
											<p>Kullanıcılarımızın gizliliği ve veri güvenliği bizim için en önemli önceliktir. Modern şifreleme teknolojileri kullanarak dosyalarınızın güvenliğini sağlıyoruz.</p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="card h-100 border-0 shadow-sm rounded-lg">
										<div class="card-body text-center p-4">
											<div class="mb-3">
												<i class="fas fa-bolt text-primary fa-3x"></i>
											</div>
											<h3 class="h4 mb-3">Hız</h3>
											<p>Optimize edilmiş sunucu altyapımız ile dosyalarınızı hızlı bir şekilde yükleyebilir ve indirebilirsiniz. Kullanıcı deneyimini en üst seviyeye çıkarmak için sürekli çalışıyoruz.</p>
										</div>
									</div>
								</div>
							</div>
							
							<div class="card shadow-sm border-0 rounded-lg p-4 mb-5" data-aos="fade-up" data-aos-delay="200">
								<div class="card-body">
									<h2 class="h3 mb-4 text-center">Ekibimiz</h2>
									<p>OpenFile, tutkulu yazılım geliştiricileri, tasarımcılar ve sistem yöneticilerinden oluşan küçük bir ekip tarafından 2023 yılında İstanbul'da kurulmuştur. Açık kaynak yazılım felsefesine inanan ekibimiz, kullanıcı geri bildirimlerini dikkate alarak sürekli olarak hizmetimizi geliştirmektedir.</p>
									<p>Ekip üyelerimiz, sektörde uzun yıllara dayanan deneyimleriyle kullanıcılarımıza en iyi hizmeti sunmak için çalışmaktadır. OpenFile'ı geliştirmeye katkıda bulunmak isterseniz, GitHub sayfamızı ziyaret edebilir ve projeye katılabilirsiniz.</p>
								</div>
							</div>
							
							<div class="card shadow-sm border-0 rounded-lg p-4 mb-5" data-aos="fade-up" data-aos-delay="300">
								<div class="card-body">
									<h2 class="h3 mb-4 text-center">Açık Kaynak Felsefemiz</h2>
									<p>OpenFile, açık kaynak bir projedir ve tüm kaynak kodlarımız GitHub üzerinde paylaşılmaktadır. Açık kaynak felsefesi, yazılımın herkes tarafından incelenebilir, değiştirilebilir ve geliştirilebilir olmasını sağlar.</p>
									<p>Bu şeffaflık sayesinde, kullanıcılarımız gizliliklerine ve verilerinin nasıl işlendiğine dair endişe duymadan hizmetimizi kullanabilirler. Ayrıca, topluluk katkılarıyla sürekli olarak gelişen bir platform oluşturmayı hedefliyoruz.</p>
									<div class="text-center mt-4">
										<a href="#" class="btn btn-primary">
											<i class="fab fa-github me-2"></i>GitHub'da İncele
										</a>
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
	<script type="text/javascript" src="<?= $site_url; ?>style/js/main.js"></script>

</body>
</html>