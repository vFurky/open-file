<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FileManager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FolderManager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/Logger.php';

if (!isset($_SESSION['user'])) {
	header('Location: /login');
	exit;
}

$current_folder_id = isset($_GET['folder']) ? (int)$_GET['folder'] : null;
$folderManager = new FolderManager($db, $_SESSION['user']['id']);

try {
	$folderPath = $current_folder_id ? $folderManager->getFolderPath($current_folder_id) : [];
	$sortPreference = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
	$contents = $folderManager->getFolderContents($current_folder_id, $sortPreference);
	$folders = $contents['folders'];
	
	$getFiles = $db->prepare("SELECT f.*, DATE_FORMAT(f.created_at, '%d.%m.%Y %H:%i') as formatted_date, DATE_FORMAT(f.expires_at, '%d.%m.%Y %H:%i') as formatted_expiry, COALESCE(f.title, f.file_name) as display_name, f.download_count, f.view_count FROM files f WHERE f.user_id = :user_id AND f.folder_id " . ($current_folder_id === null ? "IS NULL" : "= :folder_id") . " AND f.deleted_at IS NULL ORDER BY f.created_at DESC");

	$params = ['user_id' => $_SESSION['user']['id']];
	if ($current_folder_id !== null) {
		$params['folder_id'] = $current_folder_id;
	}

	$getFiles -> execute($params);
	$files = $getFiles->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
	Logger::error("[MY-FILES.PHP-17]-DOSYA_CEKME_HATASI: " . $e->getMessage());
	$error = 'Dosyalar getirilirken bir hata oluştu.';
}

function getFileIcon($fileName) {
	$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	$icons = [
		'pdf' => 'fa-file-pdf',
		'doc' => 'fa-file-word',
		'docx' => 'fa-file-word',
		'xls' => 'fa-file-excel',
		'xlsx' => 'fa-file-excel',
		'txt' => 'fa-file-alt',
		'jpg' => 'fa-file-image',
		'jpeg' => 'fa-file-image',
		'png' => 'fa-file-image',
		'gif' => 'fa-file-image',
		'zip' => 'fa-file-archive',
		'rar' => 'fa-file-archive'
	];

	return isset($icons[$extension]) ? $icons[$extension] : 'fa-file';
}

function formatFileSize($bytes) {
	$units = ['B', 'KB', 'MB', 'GB', 'TB'];
	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);
	$bytes /= pow(1024, $pow);
	return round($bytes, 2) . ' ' . $units[$pow];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Dosyalarım - <?= $site_name ?></title>
	<link rel="stylesheet" href="<?= $site_url; ?>style/css/main.css">
	<link rel="stylesheet" href="<?= $site_url; ?>style/css/files.css">
	<?php include("./files/assets/css-files.php"); ?>
</head>
<body>
	<div class="wrapper">
		
		<div id="globalUploadProgress" class="position-fixed top-0 start-0 end-0" style="display:none; z-index:1050;">
			<div class="progress" style="height: 3px; border-radius: 0;">
				<div class="progress-bar" role="progressbar" style="width: 0%"></div>
			</div>
		</div>

		<?php include("./files/assets/navbar.php"); ?>

		<div class="content">
			<header class="hero-section text-white text-center">
				<div class="container">
					<div class="row align-items-center">
						<div class="col-lg-8 mx-auto" data-aos="fade-up">
							<h1 class="display-4 fw-bold mb-4">Dosyalarım</h1>
							<div class="search-box mx-auto" data-aos="fade-up" data-aos-delay="100">
								<div class="row g-3">
									<div class="col-md-12 mb-3">
										<div class="input-group">
											<input type="text" class="form-control" id="searchFiles" placeholder="Dosya/klasör ara...">
											<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearch">
												<i class="fas fa-filter"></i>
											</button>
											<button class="btn btn-primary" id="searchButton">
												<i class="fas fa-search"></i> Ara
											</button>
										</div>
									</div>
									
									<div class="col-12">
										<div class="collapse" id="advancedSearch">
											<div class="card card-body">
												<div class="row g-3">
													<div class="col-md-6">
														<label class="form-label">Dosya Türü</label>
														<select class="form-select" id="fileType" multiple>
															<option value="pdf">PDF</option>
															<option value="doc,docx">Word</option>
															<option value="xls,xlsx">Excel</option>
															<option value="jpg,jpeg,png,gif">Resim</option>
															<option value="zip,rar">Arşiv</option>
														</select>
													</div>
													<div class="col-md-6">
														<label class="form-label">Tarih Aralığı</label>
														<div class="input-group">
															<input type="date" class="form-control" id="dateFrom">
															<span class="input-group-text">-</span>
															<input type="date" class="form-control" id="dateTo">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="view-options mt-3" data-aos="fade-up" data-aos-delay="200">
								<button class="btn btn-outline-light active" data-view="grid">
									<i class="fas fa-th-large"></i>
								</button>
								<button class="btn btn-outline-light" data-view="list">
									<i class="fas fa-list"></i>
								</button>
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

			<section class="files-section">
				<div class="container">
					<nav aria-label="breadcrumb" class="mb-4">
						<ol class="breadcrumb">
							<li class="breadcrumb-item">
								<a href="?folder=">
									<i class="fas fa-home"></i> Ana Klasör
								</a>
							</li>
							<?php foreach ($folderPath as $folder): ?>
								<li class="breadcrumb-item">
									<a href="?folder=<?= $folder['id'] ?>">
										<?= htmlspecialchars($folder['name']) ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ol>
					</nav>

					<div class="toolbar mb-4">
						<div class="d-flex justify-content-between align-items-center">
							<div class="d-flex align-items-center">
								<button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createFolderModal">
									<i class="fas fa-folder-plus"></i> Yeni Klasör
								</button>
								<div class="btn-group" id="bulkActionButtons" style="display: none;">
									<button class="btn btn-outline-danger" id="deleteSelectedBtn">
										<i class="fas fa-trash"></i> Seçilenleri Sil
									</button>
								</div>
							</div>
							<div class="d-flex align-items-center">
								<select class="form-select me-2" id="sortOptions">
									<option value="name_asc">İsim (A-Z)</option>
									<option value="name_desc">İsim (Z-A)</option>
									<option value="date_asc">Tarih (Eskiden Yeniye)</option>
									<option value="date_desc">Tarih (Yeniden Eskiye)</option>
								</select>
								<div class="btn-group">
									<button class="btn btn-outline-primary active" data-view="grid">
										<i class="fas fa-th-large"></i>
									</button>
									<button class="btn btn-outline-primary" data-view="list">
										<i class="fas fa-list"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<?php if (isset($error)): ?>
						<div class="alert alert-danger" role="alert">
							<?= htmlspecialchars($error) ?>
						</div>
					<?php endif; ?>

					<div class="files-container" data-aos="fade-up">

						<div id="dragDropZone" class="drag-drop-zone">
							<div class="drag-drop-message">
								<i class="fas fa-cloud-upload-alt"></i>
								<h3>Dosyaları buraya sürükleyin</h3>
								<p>veya dosya seçmek için tıklayın</p>
							</div>
							<input type="file" id="fileInput" multiple style="display: none;">
						</div>

						<div class="files-grid" id="filesGrid">
							<?php if (empty($folders) && empty($files)): ?>
							<div class="text-center w-100 p-5">
								<h3>Bu klasör boş</h3>
								<p>Dosya yüklemek için ana sayfadaki "Dosya Yükle" butonunu kullanabilirsiniz.</p>
							</div>
						<?php else: ?>
							<?php foreach ($folders as $folder): ?>
								<div class="folder-card" data-folder-id="<?= $folder['id'] ?>" data-item-type="folder" draggable="true">
									<div class="select-checkbox">
										<input type="checkbox" class="form-check-input item-select">
									</div>
									<div class="folder-icon">
										<i class="fas fa-folder fa-2x"></i>
									</div>
									<div class="folder-info">
										<h5 class="folder-name">
											<a href="?folder=<?= $folder['id'] ?>">
												<?= htmlspecialchars($folder['name']) ?>
											</a>
										</h5>
										<p class="folder-meta">
											<?= $folder['file_count'] ?> dosya
											<?php if ($folder['subfolder_count'] > 0): ?>
												, <?= $folder['subfolder_count'] ?> klasör
											<?php endif; ?>
										</p>
									</div>
									<div class="folder-actions">
										<button class="btn btn-sm btn-info rename-folder"  data-folder-id="<?= $folder['id'] ?>" data-folder-name="<?= htmlspecialchars($folder['name']) ?>">
											<i class="fas fa-edit"></i>
										</button>
										<button class="btn btn-sm btn-danger delete-folder" data-folder-id="<?= $folder['id'] ?>" data-folder-name="<?= htmlspecialchars($folder['name']) ?>">
											<i class="fas fa-trash"></i>
										</button>
									</div>
								</div>
							<?php endforeach; ?>

							<?php foreach ($files as $file): ?>
								<div class="file-card" data-file-id="<?= $file['id'] ?>" data-item-type="file" draggable="true">
									<div class="select-checkbox">
										<input type="checkbox" class="form-check-input item-select">
									</div>
									<div class="file-icon">
										<i class="far <?= getFileIcon($file['file_name']) ?>"></i>
									</div>
									<div class="file-info">
										<h5 class="file-name" title="<?= htmlspecialchars($file['file_name']) ?>">
											<?= htmlspecialchars($file['file_name']) ?>
										</h5>
										<p class="file-meta">
											<span class="file-size"><?= formatFileSize($file['file_size']) ?></span>
											<span class="file-date"><?= $file['formatted_date'] ?></span>
										</p>
										<div class="file-actions">
											<a href="<?= $site_url ?>download.php?token=<?= urlencode($file['share_token']) ?>" class="btn btn-sm btn-primary" title="İndir" download="<?= htmlspecialchars($file['file_name']) ?>">
												<i class="fas fa-download"></i>
											</a>
											<button class="btn btn-sm btn-secondary preview-btn" title="Önizleme" data-file-id="<?= $file['id'] ?>" data-mime-type="<?= $file['mime_type'] ?>" data-file-name="<?= htmlspecialchars($file['file_name']) ?>">
												<i class="fas fa-eye"></i>
											</button>
											<button class="btn btn-sm btn-info share-btn" title="Paylaş" data-share-url="<?= $site_url ?>share/<?= urlencode($file['share_token']) ?>">
												<i class="fas fa-share-alt"></i>
											</button>

											<button class="btn btn-sm btn-danger delete-btn" title="Sil" data-file-id="<?= $file['id'] ?>" data-file-name="<?= htmlspecialchars($file['file_name']) ?>">
												<i class="fas fa-trash-alt"></i>
											</button>
										</div>
									</div>
									<?php if ($file['expires_at']): ?>
										<div class="file-expiry" title="Son geçerlilik tarihi">
											<i class="fas fa-clock"></i>
											<?= $file['formatted_expiry'] ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	</div>

	<?php include("./files/assets/footer.php"); ?>
</div>

<?php include("./files/assets/js-files.php"); ?>

<div class="modal fade" id="shareModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Dosya Paylaşım Linki</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<input type="text" class="form-control" id="shareUrl" readonly>
					<button class="btn btn-primary" id="copyShareUrl">
						<i class="fas fa-copy"></i> Kopyala
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="createFolderModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Yeni Klasör</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<form id="createFolderForm">
					<div class="mb-3">
						<label for="folderName" class="form-label">Klasör Adı</label>
						<input type="text" class="form-control" id="folderName" required>
					</div>
					<div class="mb-3">
						<label for="folderDescription" class="form-label">Açıklama (İsteğe bağlı)</label>
						<textarea class="form-control" id="folderDescription" rows="3"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
				<button type="button" class="btn btn-primary" id="createFolderBtn">Oluştur</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="moveItemsModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Klasöre Taşı</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div id="folderTree" class="folder-tree">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
				<button type="button" class="btn btn-primary" id="moveItemsBtn">Taşı</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Dosya Önizleme</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="preview-content">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
			</div>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="<?= $site_url; ?>style/js/my-files.js"></script>
<script type="text/javascript" src="<?= $site_url; ?>style/js/main.js"></script>

</body>
</html>