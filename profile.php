<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/assets/header.php'; ?>
<?php $dob_value = (isset($user_dob) && $user_dob !== '0000-00-00') ? htmlspecialchars($user_dob) : ''; ?>

<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $username; ?> - <?= $site_name ?></title>
	<link rel="stylesheet" href="./style/css/main.css">
	<link rel="stylesheet" href="./style/css/profile.css">
	<?php include("./files/assets/css-files.php"); ?>
</head>
<body>
	<div class="wrapper">
		<?php include("./files/assets/navbar.php"); ?>

		<div class="content">
			<section class="profile-section">
				<div class="container">
					<div class="row">
						<div class="col-lg-3 mb-4">
							<div class="profile-container text-center">
								<img src="./files/images/user.png" alt="Profil Fotoğrafı" class="profile-avatar mb-3">
								<h4 class="mb-1"><?= $user_name; ?> <?= $user_surname; ?></h4>
								<p class="text-muted mb-3"><?= $_SESSION['user']['username']; ?></p>
								<hr>
								<div class="d-flex justify-content-around text-center">
									<div>
										<h5>42</h5>
										<small class="text-muted">Yüklenen Dosya</small>
									</div>
									<div>
										<h5>3.2GB</h5>
										<small class="text-muted">Toplam Boyut</small>
									</div>
								</div>
								<hr>
								<div class="text-start">
									<p class="small mb-2"><i class="fas fa-envelope me-2"></i>E-Posta: <?= $user_email; ?></p>
									<p class="small mb-2"><i class="fas fa-calendar me-2"></i>Kayıt Tarihi: <?= $reg_date; ?></p>
									<p class="small mb-2"><i class="fas fa-cake me-2"></i>Doğum Tarihi: <?= $user_dob; ?></p>
									<p class="small mb-2"><i class="fas fa-phone me-2"></i>Telefon: <?= $user_tel; ?></p>
								</div>
							</div>
						</div>
						<div class="col-lg-9">
							<div class="profile-container">
								<ul class="nav nav-tabs profile-tabs mb-4" id="profileTabs" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="files-tab" data-bs-toggle="tab" href="#files" role="tab">
											<i class="fas fa-file me-2"></i>Dosyalarım
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab">
											<i class="fas fa-cog me-2"></i>Ayarlar
										</a>
									</li>
								</ul>

								<div class="tab-content" id="profileTabsContent">
									<div class="tab-pane fade show active" id="files" role="tabpanel">
										<div class="d-flex justify-content-between align-items-center mb-4">
											<h5 class="mb-0">Yüklenen Dosyalar</h5>
											<div class="d-flex gap-2">
												<div class="dropdown">
													<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
														<i class="fas fa-sort me-1"></i> Sırala
													</button>
													<ul class="dropdown-menu dropdown-menu-end">
														<li><a class="dropdown-item" href="#">Önce En Yeni</a></li>
														<li><a class="dropdown-item" href="#">Önce En Eski</a></li>
														<li><a class="dropdown-item" href="#">En Yüksek Boyut</a></li>
														<li><a class="dropdown-item" href="#">En Küçük Boyut</a></li>
													</ul>
												</div>
												<div class="dropdown">
													<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
														<i class="fas fa-filter me-1"></i> Filtrele
													</button>
													<ul class="dropdown-menu dropdown-menu-end">
														<li><a class="dropdown-item" href="#">Tümünü Göster</a></li>
														<li><a class="dropdown-item" href="#">Görseller</a></li>
														<li><a class="dropdown-item" href="#">Dosyalar</a></li>
													</ul>
												</div>
											</div>
										</div>

										<div class="files-list">
											<div class="file-item d-flex align-items-center">
												<div class="file-icon me-3">
													<i class="far fa-file-pdf"></i>
												</div>
												<div class="flex-grow-1">
													<div class="d-flex justify-content-between align-items-center">
														<h6 class="mb-1">Proje_Sunum.pdf</h6>
														<div class="dropdown">
															<button class="btn btn-sm text-secondary" type="button" id="fileAction1" data-bs-toggle="dropdown">
																<i class="fas fa-ellipsis-v"></i>
															</button>
															<ul class="dropdown-menu dropdown-menu-end">
																<li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Download</a></li>
																<li><a class="dropdown-item" href="#"><i class="fas fa-share-alt me-2"></i>Share</a></li>
																<li><a class="dropdown-item" href="#"><i class="fas fa-link me-2"></i>Copy Link</a></li>
																<li><hr class="dropdown-divider"></li>
																<li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
															</ul>
														</div>
													</div>
													<div class="d-flex justify-content-between align-items-center">
														<p class="small text-muted mb-0">2.4 MB · 23 Mart 2025</p>
														<span class="badge bg-success">Public</span>
													</div>
												</div>
											</div>

											<div class="file-item d-flex align-items-center">
												<div class="file-icon me-3">
													<i class="far fa-file-image"></i>
												</div>
												<div class="flex-grow-1">
													<div class="d-flex justify-content-between align-items-center">
														<h6 class="mb-1">Tatil_Fotoğrafları.zip</h6>
														<div class="dropdown">
															<button class="btn btn-sm text-secondary" type="button" id="fileAction2" data-bs-toggle="dropdown">
																<i class="fas fa-ellipsis-v"></i>
															</button>
															<ul class="dropdown-menu dropdown-menu-end">
																<li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Download</a></li>
																<li><a class="dropdown-item" href="#" disabled><i class="fas fa-share-alt me-2"></i>Share</a></li>
																<li><a class="dropdown-item" href="#" disabled><i class="fas fa-link me-2"></i>Copy Link</a></li>
																<li><hr class="dropdown-divider"></li>
																<li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
															</ul>
														</div>
													</div>
													<div class="d-flex justify-content-between align-items-center">
														<p class="small text-muted mb-0">45.8 MB · 20 Mart 2025</p>
														<span class="badge bg-danger">Private</span>
													</div>
												</div>
											</div>

											<div class="file-item d-flex align-items-center">
												<div class="file-icon me-3">
													<i class="far fa-file-word"></i>
												</div>
												<div class="flex-grow-1">
													<div class="d-flex justify-content-between align-items-center">
														<h6 class="mb-1">Rapor_Son_Versiyon.docx</h6>
														<div class="dropdown">
															<button class="btn btn-sm text-secondary" type="button" id="fileAction3" data-bs-toggle="dropdown">
																<i class="fas fa-ellipsis-v"></i>
															</button>
															<ul class="dropdown-menu dropdown-menu-end">
																<li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Download</a></li>
																<li><a class="dropdown-item" href="#"><i class="fas fa-share-alt me-2"></i>Share</a></li>
																<li><a class="dropdown-item" href="#"><i class="fas fa-link me-2"></i>Copy Link</a></li>
																<li><hr class="dropdown-divider"></li>
																<li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
															</ul>
														</div>
													</div>
													<div class="d-flex justify-content-between align-items-center">
														<p class="small text-muted mb-0">1.2 MB · 15 Mart 2025</p>
														<span class="badge bg-warning text-dark">Encrypted</span>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="tab-pane fade" id="settings" role="tabpanel">
										<form id="settingsForm" class="settings-form" novalidate>
											<?php $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>
											<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
											<!-- Profil Ayarları -->
											<div class="row mb-4">
												<div class="col-12">
													<h5 class="mb-3">Profil Ayarları</h5>
												</div>

												<div class="col-md-6 mb-3">
													<label for="username">Kullanıcı Adı <span class="text-danger">*</span></label>
													<input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı Adı" value="<?= htmlspecialchars($username) ?>" required minlength="3" maxlength="20" pattern="^[A-Za-z0-9._\-]{3,20}$">
													<div class="invalid-feedback">3–20 karakter; harf, rakam, . _ - izinli.</div>
												</div>

												<div class="col-md-6 mb-3">
													<label for="email">E-Posta <span class="text-danger">*</span></label>
													<input type="email" class="form-control" id="email" name="email" placeholder="E-Posta" value="<?= htmlspecialchars($user_email) ?>" required maxlength="100">
													<div class="invalid-feedback">Lütfen geçerli bir E-Posta adresi girin.</div>
												</div>

												<div class="col-md-6 mb-3">
													<label for="user_name">İsim <span class="text-danger">*</span></label>
													<input type="text" class="form-control" id="user_name" name="user_name" placeholder="İsim" value="<?= htmlspecialchars($user_name) ?>" required maxlength="50">
													<div class="invalid-feedback">İsim boş bırakılamaz.</div>
												</div>

												<div class="col-md-6 mb-3">
													<label for="user_surname">Soyisim <span class="text-danger">*</span></label>
													<input type="text" class="form-control" id="user_surname" name="user_surname" placeholder="Soyisim" value="<?= htmlspecialchars($user_surname) ?>" required maxlength="50">
													<div class="invalid-feedback">Soyisim boş bırakılamaz.</div>
												</div>

												<div class="col-md-6 mb-3">
													<label for="phone">Telefon Numarası</label>
													<input type="tel" class="form-control" id="phone" name="phone" placeholder="05XXXXXXXXX" value="<?= htmlspecialchars($user_tel) ?>" pattern="^(?:\+90)?[5-9]\d{9}$">
													<div class="invalid-feedback">Lütfen 5XXXXXXXXX veya +90XXXXXXXXXX formatında bir numara girin.</div>
												</div>

												<div class="col-md-6 mb-3">
													<label for="dob">Doğum Tarihi</label>
													<input type="date" class="form-control" id="dob" name="dob" min="1900-01-01" max="<?= date('Y-m-d') ?>" value="<?= $dob_value ?>">
													<div class="invalid-feedback">1900–<?= date('Y') ?> arasında geçerli bir tarih girin.</div>
												</div>
											</div>

											<div class="row mb-4">
												<div class="col-12"><h5 class="mb-3">Bildirim Ayarları</h5></div>
												<div class="col-12">
													<div class="form-check form-switch mb-3">
														<input class="form-check-input" type="checkbox" id="securityNotifs" name="securityNotifs" checked>
														<label class="form-check-label" for="securityNotifs">Güvenlik Bildirimleri</label>
													</div>
													<div class="form-check form-switch mb-3">
														<input class="form-check-input" type="checkbox" id="emailNotifs" name="emailNotifs" checked>
														<label class="form-check-label" for="emailNotifs">E-Posta Yoluyla Bildirim</label>
													</div>
													<div class="form-check form-switch mb-3">
														<input class="form-check-input" type="checkbox" id="phoneNotifs" name="phoneNotifs" checked>
														<label class="form-check-label" for="phoneNotifs">SMS Yoluyla Bildirim</label>
													</div>
												</div>
											</div>

											<div class="d-flex gap-2 justify-content-end">
												<button type="button" class="btn btn-outline-secondary">İptal</button>
												<button id="saveBtn" type="submit" class="btn btn-primary">
													<span id="saveBtnText">Kaydet</span>
													<span id="saveSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
												</button>
											</div>
										</form>

										<div id="result"></div>

										<br>

										<form id="passwordForm" class="settings-form" novalidate aria-live="polite">
											<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

											<div class="row mb-4">
												<div class="col-12">
													<h5 class="mb-3">Parola Değiştir</h5>
												</div>

												<div class="col-md-6 mb-3">
													<label for="currentPassword">Mevcut Parola</label>
													<div class="input-group">
														<input type="password" class="form-control" id="currentPassword" name="currentPassword" required minlength="6" placeholder="Mevcut Parola" aria-required="true">
														<button class="btn btn-outline-secondary toggle-pass" style="border-radius: 0px 10px 10px 0px;" type="button" data-target="currentPassword" aria-label="Mevcut parolayı göster/gizle">
															👁️
														</button>
														<div class="invalid-feedback">Lütfen mevcut parolanızı girin.</div>
													</div>
												</div>

												<div class="col-md-6 mb-3"></div>

												<div class="col-md-6 mb-3">
													<label for="newPassword">Yeni Parola</label>
													<div class="input-group">
														<input type="password" class="form-control" id="newPassword" name="newPassword" required minlength="8" placeholder="Yeni Parola" aria-describedby="strengthHelp" aria-required="true">
														<button class="btn btn-outline-secondary toggle-pass" style="border-radius: 0px 10px 10px 0px;" type="button" data-target="newPassword" aria-label="Yeni parolayı göster/gizle">
															👁️
														</button>
														<div class="invalid-feedback">Yeni parola en az 8 karakter, 1 büyük, 1 küçük, 1 rakam, 1 özel karakter içermeli.</div>
													</div>

													<div id="strengthHelp" class="mt-1" aria-live="polite">
														<div class="progress">
															<div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
														</div>
														<small id="strengthText">Parola gücü: </small>
													</div>
												</div>

												<div class="col-md-6 mb-3">
													<label for="confirmPassword">Yeni Parola (Tekrar)</label>
													<div class="input-group">
														<input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required minlength="8" placeholder="Yeni Parola (Tekrar)" aria-required="true">
														<button class="btn btn-outline-secondary toggle-pass" style="border-radius: 0px 10px 10px 0px;" type="button" data-target="confirmPassword" aria-label="Tekrar parolayı göster/gizle">
															👁️
														</button>
														<div class="invalid-feedback">Girdiğiniz parolalar eşleşmiyor.</div>
													</div>
												</div>
											</div>

											<div class="d-flex gap-2 justify-content-end">
												<button type="button" class="btn btn-outline-secondary">İptal</button>
												<button id="passSaveBtn" type="submit" class="btn btn-primary">
													<span id="passSaveText">Kaydet</span>
													<span id="passSaveSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
												</button>
											</div>
										</form>

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

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
	<script type="text/javascript" src="<?= $site_url; ?>style/js/profile.js"></script>
	<script type="text/javascript" src="<?= $site_url; ?>style/js/main.js"></script>
</body>
</html>