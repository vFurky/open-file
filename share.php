<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/assets/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/Logger.php';

$share_token = isset($_GET['token']) ? preg_replace('/[^a-zA-Z0-9]/', '', $_GET['token']) : '';

if (empty($share_token)) {
	Logger::error("BOS_PAYLASIM_TOKENI_GONDERILDI: " . $_SERVER['REMOTE_ADDR']);
	header('HTTP/1.0 404 Not Found');
	exit('Dosya bulunamadı.');
}

if (strpos($_SERVER['REQUEST_URI'], '.php') !== false) {
	$clean_url = '/share/' . $share_token;
	header('Location: ' . $clean_url, true, 301);
	exit();
}

try {
	if (!$db) {
		throw new Exception("Veritabanı bağlantısı kurulamadı");
	}

	$fetchFileInfos = $db -> prepare("SELECT f.*, u.username, u.email, COALESCE(f.download_count, 0) as download_count, COALESCE(f.view_count, 0) as view_count FROM files f JOIN users u ON f.user_id = u.id WHERE f.share_token = ? AND f.deleted_at IS NULL AND (f.expires_at > UTC_TIMESTAMP() OR f.expires_at IS NULL)");

	if (!$fetchFileInfos) {
		Logger::error("[SHARE.PHP-27]-SQL_HAZIRLAMA_HATASI: " . $db -> errorInfo());
		throw new Exception('Bir hata oluştu, lütfen daha sonra tekrar deneyin.');
	}

	$fetchFileInfos -> execute([$share_token]);

	if (!$fetchFileInfos) {
		Logger::error("[SHARE.PHP-34]-SORGU_HATASI: " . $fetchFileInfos->errorInfo());
		throw new Exception('Bir hata oluştu, lütfen daha sonra tekrar deneyin.');
	}

	$file = $fetchFileInfos -> fetch(PDO::FETCH_ASSOC);

	if (!$file) {
		Logger::error("[SHARE.PHP-41]-GECERSIZ_PAYLASIM_TOKENI: " . $share_token);
		header('HTTP/1.0 404 Not Found');
		exit('Bu dosya artık bulunmuyor veya paylaşım süresi dolmuş.');
	}

	$file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file['file_path'];
	if (!file_exists($file_path)) {
		Logger::error("DOSYA_BULUNAMADI: " . $file['file_path']);
		header('HTTP/1.0 404 Not Found');
		exit('Bu dosya artık bulunmuyor veya paylaşım süresi dolmuş.');
	}

	$updateViews = $db -> prepare("UPDATE files SET view_count = view_count + 1, last_accessed = UTC_TIMESTAMP() WHERE id = ?");
	$updateViews -> execute([$file['id']]);

	$file_info = [
		'name' => $file['file_name'],
		'size' => formatFileSize($file['file_size']),
		'uploaded_by' => $file['username'],
		'upload_date' => date('d.m.Y H:i', strtotime($file['created_at'])),
		'expires_at' => $file['expires_at'] ? date('d.m.Y H:i', strtotime($file['expires_at'])) : 'Süresiz',
		'downloads' => $file['download_count'],
		'views' => $file['view_count'] + 1,
		'mime_type' => $file['mime_type'] ?? mime_content_type($file_path)
	];

	$file_extension = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
	$icon_class = getFileIconClass($file_extension);
	?>

	<!DOCTYPE html>
	<html lang="tr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= htmlspecialchars($file_info['name']) ?> - <?= $site_name ?></title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
		<link href="<?= $site_url ?>style/css/share.css" rel="stylesheet">
	</head>
	<body class="bg-light">
		
		<div class="container my-5">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<div class="file-card">
						<div class="file-preview text-center">
							<div class="file-icon-wrapper mb-4">
								<i class="<?= $icon_class ?>"></i>
							</div>
							<h2 class="file-title"><?= htmlspecialchars($file_info['name']) ?></h2>
							<?php if (!empty($file['description'])): ?>
								<p class="text-muted mt-2"><?= nl2br(htmlspecialchars($file['description'])) ?></p>
							<?php endif; ?>
						</div>

						<div class="file-stats">
							<div class="row text-center">
								<div class="col">
									<div class="stat-item">
										<i class="fas fa-download"></i>
										<span><?= number_format($file_info['downloads']) ?></span>
										<small>İndirme</small>
									</div>
								</div>
								<div class="col">
									<div class="stat-item">
										<i class="fas fa-eye"></i>
										<span><?= number_format($file_info['views']) ?></span>
										<small>Görüntülenme</small>
									</div>
								</div>
								<div class="col">
									<div class="stat-item">
										<i class="fas fa-weight-hanging"></i>
										<span><?= $file_info['size'] ?></span>
										<small>Dosya Boyutu</small>
									</div>
								</div>
							</div>
						</div>

						<div class="file-meta">
							<div class="meta-item">
								<i class="fas fa-user"></i>
								<span>Yükleyen:</span>
								<strong><?= htmlspecialchars($file_info['uploaded_by']) ?></strong>
							</div>
							<div class="meta-item">
								<i class="fas fa-calendar"></i>
								<span>Yüklenme Tarihi:</span>
								<strong><?= $file_info['upload_date'] ?></strong>
							</div>
							<div class="meta-item">
								<i class="fas fa-clock"></i>
								<span>Son Geçerlilik Tarihi:</span>
								<strong><?= $file_info['expires_at'] ?></strong>
							</div>
						</div>

						<div class="file-actions text-center mt-4">
							<a href="../download?token=<?= urlencode($share_token) ?>" class="btn btn-primary btn-lg download-btn">
								<i class="fas fa-download me-2"></i>İndir
							</a>

							<button class="btn btn-outline-primary btn-lg share-btn ms-2" onclick="copyShareLink('<?= $site_url ?>share/<?= $share_token ?>')">
								<i class="fas fa-share-alt me-2"></i>Paylaş
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script type="text/javascript" src="<?= $site_url; ?>style/js/share.js"></script>
	</body>
	</html>
	<?php
} catch (Exception $e) {
	Logger::error("SHARE_PAGE_ERROR: " . $e->getMessage());
	header('HTTP/1.0 500 Internal Server Error');
	exit('Bir hata oluştu, lütfen daha sonra tekrar deneyin.');
}