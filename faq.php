<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/assets/header.php'; ?>

<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sıkça Sorulan Sorular - <?= $site_name ?></title>
	<link rel="stylesheet" href="./style/css/main.css">
	<link rel="stylesheet" href="./style/css/faq.css">
	<?php include("./files/assets/css-files.php"); ?>
</head>
<body>
	<div class="wrapper">
		<?php include("./files/assets/navbar.php"); ?>

		<div class="content">
			<header class="faq-header text-white text-center">
				<div class="container">
					<div class="row justify-content-center">
						<div class="col-lg-7" data-aos="fade-up">
							<h1 class="display-5 fw-bold mb-4">Sıkça Sorulan Sorular</h1>
							<p class="lead mb-5">OpenFile hakkında merak ettiğiniz tüm sorulara ve yanıtlarına buradan ulaşabilirsiniz. Eğer halen merak ettiğiniz bir şey varsa lütfen iletişime geçin.</p>
							<div class="search-faq">
								<i class="fas fa-search search-icon"></i>
								<input type="text" class="form-control form-control-lg" placeholder="SSS içinde ara...">
							</div>
						</div>
					</div>
				</div>
				<div class="wave-bottom">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 150">
						<path fill="#ffffff" fill-opacity="1" d="M0,96L80,85.3C160,75,320,53,480,58.7C640,64,800,96,960,96C1120,96,1280,64,1360,48L1440,32L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
					</svg>
				</div>
			</header>

			<section class="faq-section">
				<div class="container">
					<div class="row">
						<div class="col-lg-4 mb-4">
							<div class="faq-card">
								<div class="faq-card-header">
									<h5>Kategoriler</h5>
								</div>
								<div class="faq-categories">
									<nav class="nav nav-pills flex-column">
										<a class="nav-link active" href="#general" data-bs-toggle="tab">
											<i class="fas fa-info-circle me-2"></i> Genel Sorular
										</a>
										<a class="nav-link" href="#account" data-bs-toggle="tab">
											<i class="fas fa-user-circle me-2"></i> Hesap Yönetimi
										</a>
										<a class="nav-link" href="#files" data-bs-toggle="tab">
											<i class="fas fa-file-alt me-2"></i> Dosya İşlemleri
										</a>
										<a class="nav-link" href="#sharing" data-bs-toggle="tab">
											<i class="fas fa-share-alt me-2"></i> Dosya Paylaşımı
										</a>
										<a class="nav-link" href="#security" data-bs-toggle="tab">
											<i class="fas fa-shield-alt me-2"></i> Güvenlik
										</a>
										<a class="nav-link" href="#billing" data-bs-toggle="tab">
											<i class="fas fa-credit-card me-2"></i> Ödeme ve Planlar
										</a>
									</nav>
								</div>
							</div>

							<div class="faq-card contact-card">
								<div class="text-center mb-4">
									<i class="fas fa-headset fa-3x text-primary mb-3"></i>
									<h5>Hala yardıma mı ihtiyacınız var?</h5>
									<p class="mb-0">Sorunuza yanıt bulamadıysanız, destek ekibimiz size yardımcı olmak için hazır.</p>
								</div>
								<div class="d-grid">
									<a href="#" class="btn btn-primary">
										<i class="fas fa-envelope me-2"></i> Destek Talebi Oluştur
									</a>
								</div>
							</div>
						</div>
						
						<div class="col-lg-8">
							<div class="tab-content">
								<div class="tab-pane fade show active" id="general">
									<div class="faq-card">
										<div class="faq-card-header">
											<h4 class="text-primary">Genel Sorular</h4>
											<p class="text-muted">OpenFile hakkında temel bilgiler ve sık sorulan sorular.</p>
										</div>
										
										<div class="accordion" id="accordionGeneral">
											<div class="accordion-item">
												<h2 class="accordion-header" id="headingOne">
													<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
														OpenFile nedir?
													</button>
												</h2>
												<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionGeneral">
													<div class="accordion-body">
														<p>OpenFile, tamamen açık kaynak kodlu, güvenilir, hızlı ve ücretsiz bir dosya paylaşım platformudur. Kullanıcılar dosyalarını yükleyebilir, depolayabilir ve istedikleri kişilerle güvenle paylaşabilirler. OpenFile'ın basit arayüzü, dosyalarınızı kolayca yönetmenize olanak tanır.</p>
														<p>Platform, kişisel ve profesyonel kullanıma uygun olarak tasarlanmıştır ve herhangi bir ek yazılım gerektirmez. Sadece web tarayıcınızı kullanarak tüm özelliklere erişebilirsiniz.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingTwo">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
														OpenFile'ı kimler kullanabilir?
													</button>
												</h2>
												<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionGeneral">
													<div class="accordion-body">
														<p>OpenFile, internet erişimi olan herkes tarafından kullanılabilir. Bireysel kullanıcılar, küçük işletmeler, eğitim kurumları veya büyük şirketler - herkes için uygun planlarımız bulunmaktadır.</p>
														<p>Kullanıcı dostu arayüzümüz, teknik bilgi seviyesi ne olursa olsun herkesin platformumuzu kolayca kullanabilmesini sağlar. Öğrencilerden profesyonellere, herkes OpenFile'dan faydalanabilir.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingThree">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
														OpenFile'ı kullanmak için bir yazılım yüklemem gerekiyor mu?
													</button>
												</h2>
												<div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionGeneral">
													<div class="accordion-body">
														<p>Hayır, OpenFile tamamen web tabanlı bir platformdur ve herhangi bir yazılım yüklemeniz gerekmez. Modern bir web tarayıcısı (Chrome, Firefox, Safari, Edge vb.) olan herhangi bir cihazdan OpenFile'a erişebilirsiniz.</p>
														<p>Mobil cihazlardan da (akıllı telefonlar ve tabletler) web tarayıcısı üzerinden OpenFile'ı kullanabilirsiniz. İlerleyen dönemlerde mobil uygulamalarımızı da yayınlamayı planlıyoruz.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingFour">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
														OpenFile'ın diğer dosya paylaşım hizmetlerinden farkı nedir?
													</button>
												</h2>
												<div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionGeneral">
													<div class="accordion-body">
														<p>OpenFile'ı diğer servislerden ayıran en önemli özellikler şunlardır:</p>
														<ul>
															<li><strong>Açık Kaynak Kod:</strong> Tamamen açık kaynak kodlu bir yazılımdır, bu da güvenlik ve şeffaflık sağlar.</li>
															<li><strong>Gizlilik Odaklı:</strong> Kullanıcı verilerini ve dosyalarını korumak için en ileri güvenlik önlemlerini kullanırız.</li>
															<li><strong>Reklamsız Deneyim:</strong> Platformumuzda rahatsız edici reklamlar bulunmaz.</li>
															<li><strong>Cömert Ücretsiz Plan:</strong> Diğer servislere göre daha fazla depolama alanı ve özellik sunan ücretsiz planımız vardır.</li>
															<li><strong>Hız ve Güvenilirlik:</strong> Yüksek hızlı sunucularımız ve güvenilir altyapımız kesintisiz hizmet sunar.</li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade" id="account">
									<div class="faq-card">
										<div class="faq-card-header">
											<h4 class="text-primary">Hesap Yönetimi</h4>
											<p class="text-muted">Hesap oluşturma, yönetim ve gizlilik ayarları hakkında bilgiler.</p>
										</div>
										
										<div class="accordion" id="accordionAccount">
											<div class="accordion-item">
												<h2 class="accordion-header" id="headingA1">
													<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA1" aria-expanded="true" aria-controls="collapseA1">
														Nasıl hesap oluşturabilirim?
													</button>
												</h2>
												<div id="collapseA1" class="accordion-collapse collapse show" aria-labelledby="headingA1" data-bs-parent="#accordionAccount">
													<div class="accordion-body">
														<p>OpenFile'da hesap oluşturmak oldukça basittir:</p>
														<ol>
															<li>Ana sayfadaki "Kayıt Ol" butonuna tıklayın.</li>
															<li>E-posta adresinizi, kullanıcı adınızı ve şifrenizi girin.</li>
															<li>Kullanım şartlarını ve gizlilik politikasını kabul edin.</li>
															<li>E-posta adresinize gönderilen doğrulama bağlantısına tıklayın.</li>
															<li>Hesabınız aktif hale gelecek ve hemen kullanmaya başlayabilirsiniz.</li>
														</ol>
														<p>Ayrıca Google, Facebook veya Apple hesaplarınızı kullanarak da hızlı kayıt olabilirsiniz.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingA2">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA2" aria-expanded="false" aria-controls="collapseA2">
														Şifremi unuttum, ne yapmalıyım?
													</button>
												</h2>
												<div id="collapseA2" class="accordion-collapse collapse" aria-labelledby="headingA2" data-bs-parent="#accordionAccount">
													<div class="accordion-body">
														<p>Şifrenizi unuttuysanız, kolayca sıfırlayabilirsiniz:</p>
														<ol>
															<li>Giriş sayfasındaki "Şifremi Unuttum" bağlantısına tıklayın.</li>
															<li>Kayıtlı e-posta adresinizi girin.</li>
															<li>Size bir şifre sıfırlama bağlantısı içeren bir e-posta göndereceğiz.</li>
															<li>E-postadaki bağlantıya tıklayın ve yeni şifrenizi belirleyin.</li>
														</ol>
														<p>Eğer e-postayı alamadıysanız, spam klasörünüzü kontrol edin veya destek ekibimizle iletişime geçin.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingA3">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA3" aria-expanded="false" aria-controls="collapseA3">
														Hesabımı nasıl silebilirim?
													</button>
												</h2>
												<div id="collapseA3" class="accordion-collapse collapse" aria-labelledby="headingA3" data-bs-parent="#accordionAccount">
													<div class="accordion-body">
														<p>Hesabınızı silmek için aşağıdaki adımları izleyin:</p>
														<ol>
															<li>Hesap menüsünden "Ayarlar" sayfasına gidin.</li>
															<li>Sayfanın en altındaki "Hesabı Sil" bölümüne ilerleyin.</li>
															<li>"Hesabımı Sil" butonuna tıklayın.</li>
															<li>İşlemi onaylamak için şifrenizi girmeniz istenecektir.</li>
															<li>Son onayı verdikten sonra hesabınız ve tüm verileriniz silinecektir.</li>
														</ol>
														<p><strong>Önemli Not:</strong> Hesap silme işlemi geri alınamaz. Tüm dosyalarınız ve verileriniz kalıcı olarak silinecektir. Silmeden önce saklamak istediğiniz dosyaları indirdiğinizden emin olun.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingA4">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA4" aria-expanded="false" aria-controls="collapseA4">
														Hesap bilgilerimi nasıl güncelleyebilirim?
													</button>
												</h2>
												<div id="collapseA4" class="accordion-collapse collapse" aria-labelledby="headingA4" data-bs-parent="#accordionAccount">
													<div class="accordion-body">
														<p>Hesap bilgilerinizi güncellemek için:</p>
														<ol>
															<li>Sağ üst köşedeki profil simgenize tıklayın ve "Profil" veya "Ayarlar" seçeneğini seçin.</li>
															<li>"Profil" sekmesinde kişisel bilgilerinizi (ad, soyad, profil fotoğrafı vb.) güncelleyebilirsiniz.</li>
															<li>"Ayarlar" sekmesinde e-posta adresinizi, şifrenizi ve bildirim tercihlerinizi değiştirebilirsiniz.</li>
															<li>Değişiklikleri yaptıktan sonra "Kaydet" butonuna tıklayın.</li>
														</ol>
														<p>E-posta adresinizi değiştirirseniz, yeni adresinize bir doğrulama e-postası gönderilecektir. Değişikliğin tamamlanması için bu e-postadaki bağlantıya tıklamanız gerekecektir.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade" id="files">
									<div class="faq-card">
										<div class="faq-card-header">
											<h4 class="text-primary">Dosya İşlemleri</h4>
											<p class="text-muted">Dosya yükleme, indirme ve yönetimi ile ilgili bilgiler.</p>
										</div>
										
										<div class="accordion" id="accordionFiles">
											<div class="accordion-item">
												<h2 class="accordion-header" id="headingF1">
													<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF1" aria-expanded="true" aria-controls="collapseF1">
														Nasıl dosya yükleyebilirim?
													</button>
												</h2>
												<div id="collapseF1" class="accordion-collapse collapse show" aria-labelledby="headingF1" data-bs-parent="#accordionFiles">
													<div class="accordion-body">
														<p>OpenFile'a dosya yüklemenin birkaç yolu vardır:</p>
														<ol>
															<li><strong>Sürükle-Bırak:</strong> Dosyaları bilgisayarınızdan sürükleyip doğrudan tarayıcınızdaki yükleme alanına bırakabilirsiniz.</li>
															<li><strong>Dosya Seçici:</strong> "Dosya Seç" butonuna tıklayarak bilgisayarınızdan dosya seçebilirsiniz.</li>
															<li><strong>Çoklu Yükleme:</strong> Birden fazla dosyayı aynı anda seçerek veya sürükleyerek toplu yükleme yapabilirsiniz.</li>
														</ol>
														<p>Yükleme başladıktan sonra, yükleme ilerleme çubuğu gösterilecektir. Yükleme tamamlandığında, dosyanız OpenFile hesabınızda görünecektir.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingF2">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF2" aria-expanded="false" aria-controls="collapseF2">
														En büyük dosya boyutu limiti nedir?
													</button>
												</h2>
												<div id="collapseF2" class="accordion-collapse collapse" aria-labelledby="headingF2" data-bs-parent="#accordionFiles">
													<div class="accordion-body">
														<p>Dosya boyutu limitleri hesap tipinize göre değişir:</p>
														<ul>
															<li><strong>Ücretsiz Hesap:</strong> Tek dosya için maksimum 100 MB.</li>
															<li><strong>Pro Hesap:</strong> Tek dosya için maksimum 5 GB.</li>
															<li><strong>İş Hesabı:</strong> Tek dosya için maksimum 10 GB.</li>
															<li><strong>Kurumsal Hesap:</strong> Tek dosya için maksimum 20 GB.</li>
														</ul>
														<p>Daha büyük dosyaları yüklemek isterseniz, dosyalarınızı bir arşiv formatında (ZIP, RAR) sıkıştırabilir veya birkaç parçaya bölebilirsiniz.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingF3">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF3" aria-expanded="false" aria-controls="collapseF3">
														Hangi dosya türleri destekleniyor?
													</button>
												</h2>
												<div id="collapseF3" class="accordion-collapse collapse" aria-labelledby="headingF3" data-bs-parent="#accordionFiles">
													<div class="accordion-body">
														<p>OpenFile neredeyse tüm dosya türlerini destekler, bazı yaygın olanlar şunlardır:</p>
														<ul>
															<li><strong>Dokümanlar:</strong> PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT</li>
															<li><strong>Görüntüler:</strong> JPG, JPEG, PNG, GIF, BMP, SVG, TIFF</li>
															<li><strong>Video:</strong> MP4, AVI, MOV, WMV, MKV</li>
															<li><strong>Ses:</strong> MP3, WAV, OGG, AAC, FLAC</li>
															<li><strong>Arşivler:</strong> ZIP, RAR, 7Z, TAR, GZ</li>
															<li><strong>Tasarım:</strong> PSD, AI, INDD, XD</li>
															<li><strong>Kod:</strong> HTML, CSS, JS, PHP, PY, JAVA, C, CPP</li>
														</ul>
														<p>Güvenlik nedeniyle .exe ve diğer çalıştırılabilir dosya türleri engellenmektedir.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingF4">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF4" aria-expanded="false" aria-controls="collapseF4">
														Dosyalarımı nasıl organize edebilirim?
													</button>
												</h2>
												<div id="collapseF4" class="accordion-collapse collapse" aria-labelledby="headingF4" data-bs-parent="#accordionFiles">
													<div class="accordion-body">
														<p>OpenFile'da dosyalarınızı organize etmek için birkaç yöntem sunuyoruz:</p>
														<ul>
															<li><strong>Klasörler:</strong> Dosyalarınızı kategorilere göre gruplayabileceğiniz klasörler oluşturabilirsiniz.</li>
															<li><strong>Etiketler:</strong> Dosyalarınıza etiketler ekleyerek daha sonra bu etiketlere göre filtreleme yapabilirsiniz.</li>
															<li><strong>Yıldızlı Dosyalar:</strong> Önemli dosyalarınızı yıldızlayarak hızlı erişim sağlayabilirsiniz.</li>
															<li><strong>Sıralama:</strong> Dosyalarınızı isme, boyuta, tarihe veya dosya tipine göre sıralayabilirsiniz.</li>
															<li><strong>Arama:</strong> Gelişmiş arama özelliğimizle dosya adı, içerik veya etiketlere göre arama yapabilirsiniz.</li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade" id="sharing">
									<div class="faq-card">
										<div class="faq-card-header">
											<h4 class="text-primary">Dosya Paylaşımı</h4>
											<p class="text-muted">Dosyalarınızı başkalarıyla paylaşma ile ilgili bilgiler.</p>
										</div>
										
										<div class="accordion" id="accordionSharing">
											<div class="accordion-item">
												<h2 class="accordion-header" id="headingS1">
													<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseS1" aria-expanded="true" aria-controls="collapseS1">
														Dosyalarımı nasıl paylaşabilirim?
													</button>
												</h2>
												<div id="collapseS1" class="accordion-collapse collapse show" aria-labelledby="headingS1" data-bs-parent="#accordionSharing">
													<div class="accordion-body">
														<p>OpenFile'da dosyalarınızı paylaşmanın birkaç yolu vardır:</p>
														<ol>
															<li><strong>Paylaşım Bağlantısı:</strong> Dosyanın yanındaki paylaşım simgesine tıklayarak bir paylaşım bağlantısı oluşturabilirsiniz. Bu bağlantıyı e-posta, mesajlaşma uygulamaları veya sosyal medya aracılığıyla paylaşabilirsiniz.</li>
															<li><strong>E-posta ile Paylaşım:</strong> Doğrudan OpenFile üzerinden e-posta adresleri girerek dosyanızı paylaşabilirsiniz.</li>
															<li><strong>Klasör Paylaşımı:</strong> Tüm bir klasörü paylaşarak içindeki tüm dosyalara erişim sağlayabilirsiniz.</li>
															<li><strong>QR Kod:</strong> Dosyanız için QR kod oluşturarak mobil cihazlarla hızlı paylaşım yapabilirsiniz.</li>
														</ol>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingS2">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseS2" aria-expanded="false" aria-controls="collapseS2">
														Paylaşılan dosyalara kimlerin erişebileceğini nasıl kontrol edebilirim?
													</button>
												</h2>
												<div id="collapseS2" class="accordion-collapse collapse" aria-labelledby="headingS2" data-bs-parent="#accordionSharing">
													<div class="accordion-body">
														<p>Paylaşım ayarlarınızı şu şekilde kontrol edebilirsiniz:</p>
														<ul>
															<li><strong>Herkese Açık:</strong> Bağlantıya sahip herkes dosyaya erişebilir.</li>
															<li><strong>Şifreli Paylaşım:</strong> Dosyaya erişmek için bir şifre gerekir.</li>
															<li><strong>Belirli Kişiler:</strong> Sadece belirttiğiniz e-posta adreslerine sahip kişiler erişebilir.</li>
															<li><strong>Ekip Üyeleri:</strong> Sadece ekibinizdeki üyeler erişebilir.</li>
															<li><strong>Zaman Sınırlı Erişim:</strong> Belirli bir tarihten sonra bağlantı geçersiz olur.</li>
														</ul>
														<p>Ayrıca paylaşım izinlerini "görüntüleme", "indirme" veya "düzenleme" olarak sınırlayabilirsiniz.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingS3">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseS3" aria-expanded="false" aria-controls="collapseS3">
														Paylaştığım bir dosyanın paylaşımını nasıl durdurabilirim?
													</button>
												</h2>
												<div id="collapseS3" class="accordion-collapse collapse" aria-labelledby="headingS3" data-bs-parent="#accordionSharing">
													<div class="accordion-body">
														<p>Bir dosya paylaşımını durdurmak için:</p>
														<ol>
															<li>Dosya yöneticisinde paylaştığınız dosyaya tıklayın.</li>
															<li>"Paylaşım Ayarları" veya "Paylaşım Bilgileri" seçeneğine tıklayın.</li>
															<li>"Paylaşımı Durdur" veya "Bağlantıyı Devre Dışı Bırak" butonuna tıklayın.</li>
														</ol>
														<p>Bu işlemden sonra, daha önce paylaştığınız bağlantılar artık çalışmayacak ve dosyanıza erişim sağlanamayacaktır. Dilerseniz paylaşımı tamamen iptal etmek yerine izinleri değiştirebilir veya yeni bir paylaşım bağlantısı oluşturabilirsiniz.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingS4">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseS4" aria-expanded="false" aria-controls="collapseS4">
														Kimlerin dosyalarımı görüntülediğini görebilir miyim?
													</button>
												</h2>
												<div id="collapseS4" class="accordion-collapse collapse" aria-labelledby="headingS4" data-bs-parent="#accordionSharing">
													<div class="accordion-body">
														<p>Evet, OpenFile paylaşım istatistikleri ve erişim kayıtları sunar:</p>
														<ul>
															<li><strong>Görüntülenme Sayısı:</strong> Dosyanızın kaç kez açıldığını görüntüleyebilirsiniz.</li>
															<li><strong>İndirme Sayısı:</strong> Dosyanızın kaç kez indirildiğini takip edebilirsiniz.</li>
															<li><strong>Erişim Kayıtları:</strong> Pro ve daha üstü planlarda, dosyanıza erişen kişilerin IP adresleri ve erişim zamanları gibi detaylı bilgileri görebilirsiniz.</li>
															<li><strong>E-posta Bildirimleri:</strong> Dosyanız görüntülendiğinde veya indirildiğinde e-posta bildirimi alabilirsiniz.</li>
														</ul>
														<p>Bu özellikler, "Dosya Detayları" veya "Paylaşım İstatistikleri" bölümünde bulunmaktadır.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade" id="security">
									<div class="faq-card">
										<div class="faq-card-header">
											<h4 class="text-primary">Güvenlik</h4>
											<p class="text-muted">Dosya güvenliği ve hesap koruması hakkında bilgiler.</p>
										</div>
										
										<div class="accordion" id="accordionSecurity">
											<div class="accordion-item">
												<h2 class="accordion-header" id="headingSec1">
													<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSec1" aria-expanded="true" aria-controls="collapseSec1">
														Dosyalarım ne kadar güvende?
													</button>
												</h2>
												<div id="collapseSec1" class="accordion-collapse collapse show" aria-labelledby="headingSec1" data-bs-parent="#accordionSecurity">
													<div class="accordion-body">
														<p>OpenFile'da güvenlik önceliğimizdir ve dosyalarınızı korumak için çeşitli önlemler alırız:</p>
														<ul>
															<li><strong>Uçtan Uca Şifreleme:</strong> Tüm dosyalarınız yükleme, depolama ve indirme sırasında şifrelenir.</li>
															<li><strong>SSL/TLS Koruması:</strong> Tüm veri transferleri güvenli bağlantılar üzerinden yapılır.</li>
															<li><strong>Çok Katmanlı Güvenlik:</strong> Verilerinizi korumak için birden fazla güvenlik katmanı kullanırız.</li>
															<li><strong>Düzenli Güvenlik Denetimleri:</strong> Sistemlerimiz düzenli olarak bağımsız güvenlik uzmanları tarafından denetlenir.</li>
															<li><strong>Coğrafi Yedekleme:</strong> Dosyalarınız çeşitli güvenli veri merkezlerinde yedeklenir.</li>
														</ul>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingSec2">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSec2" aria-expanded="false" aria-controls="collapseSec2">
														İki faktörlü kimlik doğrulamayı nasıl etkinleştirebilirim?
													</button>
												</h2>
												<div id="collapseSec2" class="accordion-collapse collapse" aria-labelledby="headingSec2" data-bs-parent="#accordionSecurity">
													<div class="accordion-body">
														<p>İki faktörlü kimlik doğrulamayı (2FA) etkinleştirmek için:</p>
														<ol>
															<li>Hesap ayarlarınıza gidin ve "Güvenlik" sekmesini seçin.</li>
															<li>"İki Faktörlü Kimlik Doğrulama" bölümünde "Etkinleştir" butonuna tıklayın.</li>
															<li>Tercih ettiğiniz doğrulama yöntemini seçin:
																<ul>
																	<li>SMS ile kod alma</li>
																	<li>E-posta ile kod alma</li>
																	<li>Kimlik doğrulama uygulaması (Google Authenticator, Microsoft Authenticator, Authy vb.)</li>
																</ul>
															</li>
															<li>Seçtiğiniz yönteme göre verilen talimatları izleyin.</li>
															<li>Yedek kurtarma kodlarınızı güvenli bir yerde saklayın.</li>
														</ol>
														<p>2FA etkinleştirildikten sonra, her oturum açışınızda şifrenize ek olarak bir doğrulama kodu girmeniz istenecektir.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingSec3">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSec3" aria-expanded="false" aria-controls="collapseSec3">
														Dosyalarımı şifreleyebilir miyim?
													</button>
												</h2>
												<div id="collapseSec3" class="accordion-collapse collapse" aria-labelledby="headingSec3" data-bs-parent="#accordionSecurity">
													<div class="accordion-body">
														<p>Evet, OpenFile'da dosyalarınızı ek bir şifreleme katmanıyla koruyabilirsiniz:</p>
														<ul>
															<li><strong>Dosya Şifreleme:</strong> Önemli dosyalarınızı OpenFile'a yüklemeden önce şifreleyebilirsiniz. Yükleme sırasında veya dosya detaylarında "Şifreleme Ekle" seçeneğini kullanarak dosyaya özel bir şifre belirleyebilirsiniz.</li>
															<li><strong>Klasör Şifreleme:</strong> Pro ve üzeri planlarda tüm klasörlerinizi şifreleyebilirsiniz.</li>
															<li><strong>Otomatik Şifreleme:</strong> İş ve Kurumsal planlarda, belirli dosya türleri için otomatik şifreleme politikaları oluşturabilirsiniz.</li>
														</ul>
														<p>Şifrelenmiş dosyalarınız, şifreyi bilmeyen hiç kimse tarafından görüntülenemez veya indirilemez.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingSec4">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSec4" aria-expanded="false" aria-controls="collapseSec4">
														OpenFile'ın gizlilik politikası nedir?
													</button>
												</h2>
												<div id="collapseSec4" class="accordion-collapse collapse" aria-labelledby="headingSec4" data-bs-parent="#accordionSecurity">
													<div class="accordion-body">
														<p>OpenFile olarak kullanıcılarımızın gizliliğine büyük önem veriyoruz:</p>
														<ul>
															<li><strong>Veri Toplama:</strong> Sadece hizmetlerimizi sağlamak için gereken minimum bilgiyi toplarız.</li>
															<li><strong>Dosya İçeriği:</strong> Dosyalarınızın içeriğini taramaz veya analiz etmeyiz.</li>
															<li><strong>Veri Satışı:</strong> Kişisel bilgilerinizi veya dosya verilerinizi üçüncü taraflarla paylaşmaz veya satmayız.</li>
															<li><strong>Şeffaflık:</strong> Hangi verileri topladığımız ve nasıl kullandığımız konusunda tamamen şeffafız.</li>
															<li><strong>Veri Kontrolü:</strong> Hesap ayarlarınızdan topladığımız verileri görüntüleyebilir ve silebilirsiniz.</li>
														</ul>
														<p>Detaylı gizlilik politikamıza web sitemizin alt kısmındaki "Gizlilik Politikası" bağlantısından ulaşabilirsiniz.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade" id="billing">
									<div class="faq-card">
										<div class="faq-card-header">
											<h4 class="text-primary">Ödeme ve Planlar</h4>
											<p class="text-muted">Abonelikler, ödeme bilgileri ve plan yükseltmeleri hakkında bilgiler.</p>
										</div>
										
										<div class="accordion" id="accordionBilling">
											<div class="accordion-item">
												<h2 class="accordion-header" id="headingB1">
													<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB1" aria-expanded="true" aria-controls="collapseB1">
														Hangi abonelik planları sunuyorsunuz?
													</button>
												</h2>
												<div id="collapseB1" class="accordion-collapse collapse show" aria-labelledby="headingB1" data-bs-parent="#accordionBilling">
													<div class="accordion-body">
														<p>OpenFile'da ihtiyaçlarınıza uygun çeşitli planlar sunuyoruz:</p>
														<ul>
															<li><strong>Ücretsiz Plan:</strong> 5 GB depolama, 100 MB maksimum dosya boyutu, temel paylaşım özellikleri.</li>
															<li><strong>Pro Plan:</strong> 100 GB depolama, 5 GB maksimum dosya boyutu, gelişmiş paylaşım ve güvenlik özellikleri.</li>
															<li><strong>İş Planı:</strong> 1 TB depolama, 10 GB maksimum dosya boyutu, ekip işbirliği özellikleri, admin paneli.</li>
															<li><strong>Kurumsal Plan:</strong> 5 TB+ depolama, 20 GB maksimum dosya boyutu, özel destek, API erişimi, gelişmiş güvenlik.</li>
														</ul>
														<p>Tüm planların detaylarını ve güncel fiyatlandırmayı web sitemizdeki "Planlar" sayfasında bulabilirsiniz.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingB2">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB2" aria-expanded="false" aria-controls="collapseB2">
														Planımı nasıl yükseltebilir veya düşürebilirim?
													</button>
												</h2>
												<div id="collapseB2" class="accordion-collapse collapse" aria-labelledby="headingB2" data-bs-parent="#accordionBilling">
													<div class="accordion-body">
														<p>Planınızı değiştirmek için:</p>
														<ol>
															<li>Hesap ayarlarınıza gidin ve "Abonelik" veya "Planlar" sekmesini seçin.</li>
															<li>Mevcut planınızı ve diğer mevcut planları göreceksiniz.</li>
															<li>Yükseltmek istediğiniz planın yanındaki "Yükselt" butonuna tıklayın veya daha düşük bir plan için "Plan Değiştir" seçeneğini kullanın.</li>
															<li>Ödeme bilgilerinizi onaylayın veya güncelleyin.</li>
															<li>İşlemi tamamlamak için "Onayla" butonuna tıklayın.</li>
														</ol>
														<p><strong>Not:</strong> Plan yükseltmeleri hemen geçerli olur ve fiyat farkı orantılı olarak hesaplanır. Plan düşürmeler ise mevcut abonelik döneminin sonunda geçerli olur.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingB3">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB3" aria-expanded="false" aria-controls="collapseB3">
														Hangi ödeme yöntemlerini kabul ediyorsunuz?
													</button>
												</h2>
												<div id="collapseB3" class="accordion-collapse collapse" aria-labelledby="headingB3" data-bs-parent="#accordionBilling">
													<div class="accordion-body">
														<p>OpenFile olarak aşağıdaki ödeme yöntemlerini kabul ediyoruz:</p>
														<ul>
															<li><strong>Kredi/Banka Kartları:</strong> Visa, Mastercard, American Express</li>
															<li><strong>Dijital Cüzdanlar:</strong> PayPal, Apple Pay, Google Pay</li>
															<li><strong>Banka Havalesi:</strong> İş ve Kurumsal planlar için</li>
															<li><strong>Kripto Para:</strong> Bitcoin, Ethereum (Pro ve üzeri planlar için)</li>
														</ul>
														<p>Tüm ödemeler güvenli ödeme ağları üzerinden işlenir ve kart bilgileriniz OpenFile sistemlerinde saklanmaz.</p>
													</div>
												</div>
											</div>

											<div class="accordion-item">
												<h2 class="accordion-header" id="headingB4">
													<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB4" aria-expanded="false" aria-controls="collapseB4">
														İade politikanız nedir?
													</button>
												</h2>
												<div id="collapseB4" class="accordion-collapse collapse" aria-labelledby="headingB4" data-bs-parent="#accordionBilling">
													<div class="accordion-body">
														<p>OpenFile'ın iade politikası şu şekildedir:</p>
														<ul>
															<li><strong>14 Gün İade Garantisi:</strong> Yeni abonelikler için satın alma tarihinden itibaren 14 gün içinde tam iade sunuyoruz.</li>
															<li><strong>Hizmet Sorunları:</strong> Uzun süreli hizmet kesintileri veya teknik sorunlar yaşarsanız, etkilenen süre için orantılı iade alabilirsiniz.</li>
															<li><strong>Kullanılmamış Dönemler:</strong> Yıllık plan alıp iptal ederseniz, kullanılmayan aylar için orantılı iade yapılır.</li>
														</ul>
														<p>İade talebi oluşturmak için destek ekibimizle iletişime geçin. İadeler genellikle orijinal ödeme yönteminize 5-10 iş günü içinde yapılır.</p>
													</div>
												</div>
											</div>
										</div>
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
	<script type="text/javascript" src="<?= $site_url; ?>style/js/faq.js"></script>
	<script type="text/javascript" src="<?= $site_url; ?>style/js/main.js"></script>
	
</body>
</html>