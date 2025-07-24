<?php

class FolderManager {
	private $db;
	private $user_id;

	public function __construct($db, $user_id) {
		$this->db = $db;
		$this->user_id = $user_id;
	}

	public function createFolder($name, $parent_id = null, $description = null) {

		try {
			$name = trim($name);
			if (empty($name)) {
				throw new Exception('Lütfen bir klasör adı girin!');
			}

			if ($parent_id === 0 || $parent_id === '0' || $parent_id === '' || $parent_id === null) {
				$parent_id = null;
			}

			if ($parent_id !== null) {
				if (!$this->isValidParentFolder($parent_id)) {
					throw new Exception('Geçersiz üst klasör.');
				}
			}

			if ($this->folderExists($name, $parent_id)) {
				throw new Exception('Bu isimde bir klasör zaten mevcut!');
			}

			$createFolder = $this->db->prepare("INSERT INTO folders (user_id, parent_id, name, description, created_at) VALUES (:user_id, :parent_id, :name, :description, UTC_TIMESTAMP())");
			$createFolder -> execute([
				':user_id' => $this->user_id,
				':parent_id' => $parent_id,
				':name' => $name,
				':description' => $description
			]);

			return $this->db->lastInsertId();
		} catch (Exception $e) {
			Logger::error("KLASOR_OLUSTURMA_HATASI: " . $e->getMessage());
			throw $e;
		}
	}

	public function getFolderPath($folder_id) {
		$path = [];
		$current_id = $folder_id;

		while ($current_id !== null) {
			$getFolderPath = $this->db->prepare("SELECT id, parent_id, name FROM folders WHERE id = ? AND user_id = ?");
			$getFolderPath -> execute([$current_id, $this->user_id]);
			$folder = $getFolderPath -> fetch(PDO::FETCH_ASSOC);

			if (!$folder) break;

			array_unshift($path, $folder);
			$current_id = $folder['parent_id'];
		}

		return $path;
	}

	public function getFolderContents($folder_id = null, $sort = 'name_asc', $filters = []) {
		try {
			$params = ['user_id' => $this->user_id];

			if ($folder_id !== null) {
				$params['folder_id'] = $folder_id;
			}

			$folderQuery = "SELECT folders.*, (SELECT COUNT(*) FROM files WHERE folder_id = folders.id AND deleted_at IS NULL) as file_count, (SELECT COUNT(*) FROM folders f2 WHERE f2.parent_id = folders.id AND f2.deleted_at IS NULL) as subfolder_count, DATE_FORMAT(folders.created_at, '%d.%m.%Y %H:%i') as formatted_date FROM folders WHERE folders.user_id = :user_id AND folders.deleted_at IS NULL";

			$fileQuery = "SELECT f.*, DATE_FORMAT(f.created_at, '%d.%m.%Y %H:%i') as formatted_date, DATE_FORMAT(f.expires_at, '%d.%m.%Y %H:%i') as formatted_expiry, COALESCE(f.title, f.file_name) as display_name, f.download_count, f.view_count FROM files f WHERE f.user_id = :user_id AND f.deleted_at IS NULL";

			if ($folder_id !== null) {
				$folderQuery .= " AND folders.parent_id = :folder_id";
				$fileQuery .= " AND f.folder_id = :folder_id";
			} else {
				$folderQuery .= " AND folders.parent_id IS NULL";
				$fileQuery .= " AND f.folder_id IS NULL";
			}

			if (!empty($filters['search'])) {
				$params['search'] = '%' . $filters['search'] . '%';
				$folderQuery .= " AND folders.name LIKE :search";
				$fileQuery .= " AND (f.file_name LIKE :search OR f.title LIKE :search)";
			}

			$orderBy = $this->getOrderByClause($sort);
			$folderQuery .= " ORDER BY " . $orderBy['folder'];
			$fileQuery .= " ORDER BY " . $orderBy['file'];
			$getFolders = $this->db->prepare($folderQuery);
			$getFolders->execute($params);
			$folders = $getFolders->fetchAll(PDO::FETCH_ASSOC);
			$getFiles = $this->db->prepare($fileQuery);
			$getFiles->execute($params);
			$files = $getFiles->fetchAll(PDO::FETCH_ASSOC);

			return [
				'folders' => $folders,
				'files' => $files
			];
		} catch (Exception $e) {
			Logger::error("KLASOR_ICERIK_HATASI: " . $e->getMessage());
			throw $e;
		}
	}

	private function getOrderByClause($sort) {
		return match($sort) {
			'name_asc' => ['folder' => 'name ASC', 'file' => 'file_name ASC'],
			'name_desc' => ['folder' => 'name DESC', 'file' => 'file_name DESC'],
			'date_asc' => ['folder' => 'created_at ASC', 'file' => 'created_at ASC'],
			'date_desc' => ['folder' => 'created_at DESC', 'file' => 'created_at DESC'],
			default => ['folder' => 'name ASC', 'file' => 'created_at DESC']
		};
	}

	private function folderExists($name, $parent_id) {
		$params = [$this->user_id, $name];
		if ($parent_id !== null) {
			$params[] = $parent_id;
		}

		$folderExists = $this->db->prepare("SELECT COUNT(*) FROM folders WHERE user_id = ? AND name = ? AND deleted_at IS NULL AND " . 
			($parent_id === null ? "parent_id IS NULL" : "parent_id = ?"));
		$folderExists->execute($params);
		return $folderExists -> fetchColumn() > 0;
	}

	private function isValidParentFolder($parent_id) {
		$isValidParentFolder = $this->db->prepare("SELECT COUNT(*) FROM folders WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
		$isValidParentFolder -> execute([$parent_id, $this->user_id]);
		return $isValidParentFolder -> fetchColumn() > 0;
	}

	public function moveFile($file_id, $folder_id = null) {
		try {
			$fileCheck = $this->db->prepare("SELECT id FROM files WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
			$fileCheck -> execute([$file_id, $this->user_id]);
			if (!$fileCheck -> fetch()) {
				throw new Exception('Dosya artık bulunmuyor veya süresi dolmuş.');
			}

			if ($folder_id !== null) {
				if (!$this->isValidParentFolder($folder_id)) {
					throw new Exception('Geçersiz hedef klasör.');
				}
			}

			$moveFile = $this->db->prepare("UPDATE files SET folder_id = ?, updated_at = UTC_TIMESTAMP() WHERE id = ? AND user_id = ?");
			return $moveFile -> execute([$folder_id, $file_id, $this->user_id]);
		} catch (Exception $e) {
			Logger::error("KLASOR_TASIMA_HATASI: " . $e->getMessage());
			throw $e;
		}
	}

	public function renameFolder($folderId, $newName) {
		try {
			$newName = trim($newName);
			if (empty($newName)) {
				throw new Exception('Lütfen bir klasör adı girin!');
			}

			$renameFolder = $this->db->prepare("SELECT parent_id FROM folders WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
			$renameFolder -> execute([$folderId, $this->user_id]);
			$parent_id = $renameFolder -> fetchColumn();

			if ($this->folderExists($newName, $parent_id)) {
				throw new Exception('Zaten bu isimde bir klasör mevcut!');
			}

			$renameFolder2 = $this->db->prepare("UPDATE folders SET name = ?, updated_at = UTC_TIMESTAMP() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
			$renameFolder2 -> execute([$newName, $folderId, $this->user_id]);

			if ($renameFolder2 -> rowCount() === 0) {
				throw new Exception('Klasör bulunamadı veya değiştirme yetkiniz yok.');
			}

			return true;
		} catch (Exception $e) {
			Logger::error("KLASOR_YENIDEN_ADLANDIRMA_HATASI: " . $e->getMessage());
			throw $e;
		}
	}

	public function deleteFolder($folderId) {
		try {
			$this->db->beginTransaction();

			$deleteFolder = $this->db->prepare("SELECT COUNT(*) FROM folders WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
			$deleteFolder->execute([$folderId, $this->user_id]);

			if ($deleteFolder->fetchColumn() === 0) {
				throw new Exception('Klasör bulunamadı veya silme yetkiniz yok.');
			}

			$this->recursiveDelete($folderId);
			$this->db->commit();
			return true;
		} catch (Exception $e) {
			$this->db->rollBack();
			Logger::error("KLASOR_SILME_HATASI: " . $e->getMessage());
			throw $e;
		}
	}

	private function recursiveDelete($folderId) {
		$recursiveDelete = $this->db->prepare("SELECT id FROM folders WHERE parent_id = ? AND user_id = ? AND deleted_at IS NULL");
		$recursiveDelete -> execute([$folderId, $this->user_id]);

		while ($row = $recursiveDelete -> fetch(PDO::FETCH_ASSOC)) {
			$this->recursiveDelete($row['id']);
		}

		$recursiveDelete2 = $this->db->prepare("UPDATE files SET deleted_at = UTC_TIMESTAMP() WHERE folder_id = ? AND user_id = ? AND deleted_at IS NULL");
		$recursiveDelete2 -> execute([$folderId, $this->user_id]);

		$recursiveDelete3 = $this->db->prepare("UPDATE folders SET deleted_at = UTC_TIMESTAMP() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
		$recursiveDelete3 -> execute([$folderId, $this->user_id]);
	}

	private function getFolderInfo($folder_id) {
		$getFolderInfo = $this->db->prepare("SELECT * FROM folders WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
		$getFolderInfo -> execute([$folder_id, $this->user_id]);
		return $getFolderInfo -> fetch(PDO::FETCH_ASSOC);
	}

	public function moveFolder($folderId, $newParentId = null) {
		try {
			$moveFolder1 = $this->db->prepare("SELECT parent_id FROM folders WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
			$moveFolder1 -> execute([$folderId, $this->user_id]);
			$currentFolder = $moveFolder1 -> fetch(PDO::FETCH_ASSOC);

			if (!$currentFolder) {
				throw new Exception('Klasör bulunamadı.');
			}

			if ($newParentId !== null) {
				if ($folderId == $newParentId) {
					throw new Exception('Klasör kendisinin içine taşınamaz.');
				}

				$moveFolder2 = $this->db->prepare("SELECT id FROM folders WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
				$moveFolder2 -> execute([$newParentId, $this->user_id]);
				if (!$moveFolder2 -> fetch()) {
					throw new Exception('Hedef klasör bulunamadı.');
				}

				if ($this->isSubfolder($folderId, $newParentId)) {
					throw new Exception('Klasör kendi alt klasörüne taşınamaz.');
				}
			}

			$moveFolder3 = $this->db->prepare("UPDATE folders SET parent_id = ?, updated_at = UTC_TIMESTAMP() WHERE id = ? AND user_id = ?");
			$moveFolder3 -> execute([$newParentId, $folderId, $this->user_id]);

			return true;
		} catch (Exception $e) {
			Logger::error("KLASOR_TASIMA_HATASI: " . $e->getMessage());
			throw $e;
		}
	}

	private function isSubfolder($parentId, $childId) {
		$isSubfolder = $this->db->prepare("
			WITH RECURSIVE folder_path AS (SELECT id, parent_id FROM folders WHERE id = ? 
				AND user_id = ? 
				AND deleted_at 
				IS NULL UNION ALL SELECT 
				f.id, f.parent_id 
				FROM folders f 
				INNER JOIN folder_path fp 
				ON f.id = fp.parent_id 
				WHERE f.user_id = ? 
				AND f.deleted_at 
				IS NULL) 
			SELECT COUNT(*) 
			FROM folder_path 
			WHERE id = ?"
		);
		$isSubfolder -> execute([$childId, $this->user_id, $this->user_id, $parentId]);
		return $isSubfolder -> fetchColumn() > 0;
	}
}