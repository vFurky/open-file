/**
 * Last Updated: 2025-07-03 11:50:51
 * Author: vFurky
 */

if (!Object.entries) {
    Object.entries = function(obj) {
        var ownProps = Object.keys(obj),
        i = ownProps.length,
        resArray = new Array(i);
        while (i--)
          resArray[i] = [ownProps[i], obj[ownProps[i]]];
      return resArray;
  };
}

if (!Object.fromEntries) {
    Object.fromEntries = function(entries) {
        if (!entries || !entries[Symbol.iterator]) { throw new Error('Object.fromEntries requires a single iterable argument'); }
        var obj = {};
        for (var [key, value] of entries) {
          obj[key] = value;
      }
      return obj;
  };
}

var CONFIG = {
    API_ENDPOINTS: {
        CREATE_FOLDER: '/create-folder.php',
        DELETE_FOLDER: '/delete-folder.php',
        RENAME_FOLDER: '/rename-folder.php',
        MOVE_FILE: '/move-file.php',
        MOVE_FOLDER: '/move-folder.php'
    },
    PAGINATION: {
        ITEMS_PER_PAGE: 20,
        SCROLL_THRESHOLD: 200,
        SCROLL_DELAY: 100
    },
    SEARCH_DELAY: 300,
    DEFAULT_VIEW: 'grid',
    DEFAULT_SORT: 'name_asc',
    MAX_CONCURRENT: 3,
    QUEUE_SIZE: 10
};

function escapeHtml(unsafe) {
    return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function loadFolderContents(folderId, page) {
    page = page || 1;
    
    var url = new URL(CONFIG.API_ENDPOINTS.GET_FOLDER_CONTENTS, window.location.origin);
    var params = new URLSearchParams();
    
    if (folderId) {
        params.append('folder_id', folderId);
    }
    params.append('page', page);
    params.append('per_page', CONFIG.PAGINATION.ITEMS_PER_PAGE);
    
    url.search = params.toString();

    app.pagination.isLoading = true;
    showLoadingIndicator();

    fetch(url)
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.status === 'success') {
            app.setCurrentFolder(folderId);
            
            if (page === 1) {
                renderBreadcrumb(data.path);
                clearFolderContents();
            }
            
            app.pagination.hasMore = data.pagination.has_more;
            app.pagination.currentPage = page;
            
            renderFolderContents(data.contents, page > 1);
            initializeDragAndDrop();
        } else {
            throw new Error(data.message || 'Klasör içeriği yüklenemedi');
        }
    })
    .catch(function(error) {
        NotificationManager.showError('Klasör içeriği yüklenemedi: ' + error.message);
    })
    .finally(function() {
        app.pagination.isLoading = false;
        hideLoadingIndicator();
    });
}

function renderBreadcrumb(path) {
    var breadcrumb = document.getElementById('folderBreadcrumb');
    if (!breadcrumb) return;

    var html = '<li class="breadcrumb-item">' +
    '<a href="javascript:void(0)" onclick="loadFolderContents(null)">' +
    '<i class="fas fa-home"></i> Ana Klasör' +
    '</a></li>';

    if (path && path.length) {
        path.forEach(function(folder) {
            html += '<li class="breadcrumb-item">' +
            '<a href="javascript:void(0)" onclick="loadFolderContents(' + folder.id + ')">' +
            escapeHtml(folder.name) +
            '</a></li>';
        });
    }

    breadcrumb.innerHTML = html;
}

function renderFolderContents(contents, append) {
    var container = document.querySelector('.files-grid');
    if (!container) return;

    if (!contents || (!contents.folders.length && !contents.files.length)) {
        if (!append) {
            container.innerHTML = '<div class="empty-message text-center w-100 p-5">' +
            '<h3>Bu klasör boş</h3>' +
            '<p>Henüz bu klasörde dosya veya klasör bulunmuyor.</p></div>';
        }
        return;
    }

    var html = '';

    if (contents.folders && contents.folders.length) {
        contents.folders.forEach(function(folder) {
            html += generateFolderCard(folder);
        });
    }

    if (contents.files && contents.files.length) {
        contents.files.forEach(function(file) {
            html += generateFileCard(file);
        });
    }

    if (append) {
        container.insertAdjacentHTML('beforeend', html);
    } else {
        container.innerHTML = html;
    }
}

function showLoadingIndicator() {
    var container = document.querySelector('.files-container');
    if (!container) return;

    var existingLoader = document.querySelector('.loading-indicator');
    if (existingLoader) return;

    var loader = document.createElement('div');
    loader.className = 'loading-indicator';
    loader.innerHTML = '<div class="spinner-border text-primary" role="status">' +
    '<span class="visually-hidden">Yükleniyor...</span></div>';
    
    container.appendChild(loader);
}

function hideLoadingIndicator() {
    var loader = document.querySelector('.loading-indicator');
    if (loader) {
        loader.remove();
    }
}

function clearFolderContents() {
    var container = document.querySelector('.files-grid');
    if (container) {
        container.innerHTML = '';
    }
}

function initializeInfiniteScroll() {
    var container = document.querySelector('.files-container');
    if (!container) return;

    container.addEventListener('scroll', handleScroll);
    container.addEventListener('touchmove', handleScroll);
}

function handleScroll(event) {
    if (app.pagination.isLoading || !app.pagination.hasMore) return;

    var now = Date.now();
    if (now - app.pagination.lastScrollTime < CONFIG.PAGINATION.SCROLL_DELAY) return;
    app.pagination.lastScrollTime = now;

    var container = event.target;
    var scrollPosition = container.scrollTop + container.clientHeight;
    var scrollThreshold = container.scrollHeight - CONFIG.PAGINATION.SCROLL_THRESHOLD;

    if (scrollPosition > scrollThreshold) {
        loadFolderContents(app.currentFolderId, app.pagination.currentPage + 1);
    }
}

var style = document.createElement('style');
style.textContent = `
    .files-container {
        position: relative;
        max-height: 800px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .loading-indicator {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        background: rgba(255, 255, 255, 0.9);
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
`;
document.head.appendChild(style);

function generateFolderCard(folder) {
    return '<div class="folder-card" data-folder-id="' + folder.id + '" data-item-type="folder" draggable="true">' +
    '<div class="card">' +
    '<div class="card-body">' +
    '<div class="d-flex align-items-center">' +
    '<i class="fas fa-folder fa-2x text-warning me-3"></i>' +
    '<div class="flex-grow-1">' +
    '<h5 class="card-title folder-name mb-1">' +
    '<a href="javascript:void(0)" onclick="loadFolderContents(' + folder.id + ')">' +
    escapeHtml(folder.name) +
    '</a></h5>' +
    '<p class="card-text small text-muted mb-0">' +
    folder.file_count + ' dosya, ' + folder.subfolder_count + ' klasör' +
    '</p>' +
    '</div>' +
    '<div class="dropdown">' +
    '<button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">' +
    '<i class="fas fa-ellipsis-v"></i>' +
    '</button>' +
    '<ul class="dropdown-menu dropdown-menu-end">' +
    '<li><a class="dropdown-item rename-folder" href="javascript:void(0)" ' +
    'data-folder-id="' + folder.id + '" data-folder-name="' + escapeHtml(folder.name) + '">' +
    '<i class="fas fa-edit me-2"></i>Yeniden Adlandır</a></li>' +
    '<li><a class="dropdown-item delete-folder" href="javascript:void(0)" ' +
    'data-folder-id="' + folder.id + '" data-folder-name="' + escapeHtml(folder.name) + '">' +
    '<i class="fas fa-trash-alt me-2"></i>Sil</a></li>' +
    '</ul>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';
}

function generateFileCard(file) {
    return `<div class="file-card" data-file-id="${file.id}" data-item-type="file" data-file-name="${escapeHtml(file.file_name)}" data-created-at="${file.created_at}" data-mime-type="${file.mime_type}" draggable="true">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file fa-2x text-primary me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="card-title file-name mb-1">${escapeHtml(file.file_name)}</h5>
                        <p class="card-text small text-muted mb-0">
                            Yüklenme: ${file.formatted_date}
                            ${file.expires_at ? ' | Bitiş: ' + file.formatted_expiry : ''}
                        </p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/download.php?token=${encodeURIComponent(file.share_token)}">
                                    <i class="fas fa-download me-2"></i>İndir
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item preview-btn" href="javascript:void(0)">
                                    <i class="fas fa-eye me-2"></i>Önizleme
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item share-btn" href="javascript:void(0)" data-share-url="${file.share_url}">
                                    <i class="fas fa-share-alt me-2"></i>Paylaş
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item delete-btn" href="javascript:void(0)" data-file-id="${file.id}" data-file-name="${escapeHtml(file.file_name)}">
                                    <i class="fas fa-trash-alt me-2"></i>Sil
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
</div>`;
}

function AppState() {
    this.currentFolderId = new URLSearchParams(window.location.search).get('folder');
    this.selectedItems = new Set();
    this.viewPreference = localStorage.getItem('viewPreference') || CONFIG.DEFAULT_VIEW;
    this.sortPreference = localStorage.getItem('sortPreference') || CONFIG.DEFAULT_SORT;
    this.searchTimeout = null;
    this.pagination = {
        currentPage: 1,
        hasMore: true,
        isLoading: false,
        lastScrollTime: 0
    };
}

AppState.prototype.setCurrentFolder = function(folderId) {
    this.currentFolderId = folderId;
};

AppState.prototype.getUrlParams = function() {
    return new URLSearchParams(window.location.search);
};

AppState.prototype.updateUrl = function(params) {
    var url = new URL(window.location.href);
    Object.entries(params).forEach(function(entry) {
        var key = entry[0];
        var value = entry[1];
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
    });
    window.location.href = url.toString();
};

var APIManager = {
    makeRequest: function(endpoint, data, method) {
        method = method || 'POST';
        return fetch(endpoint, {
            method: method,
            headers: { 
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify(data)
        }).then(function(response) {
            return response.json();
        });
    },

    createFolder: function(name, description, parentId) {
        return this.makeRequest(CONFIG.API_ENDPOINTS.CREATE_FOLDER, {
            name: name,
            description: description,
            parent_id: parentId
        });
    },

    deleteFolder: function(folderId) {
        return this.makeRequest(CONFIG.API_ENDPOINTS.DELETE_FOLDER, {
            folder_id: folderId
        });
    },

    renameFolder: function(folderId, newName) {
        return this.makeRequest(CONFIG.API_ENDPOINTS.RENAME_FOLDER, {
            folder_id: folderId,
            new_name: newName
        });
    },

    moveFile: function(fileId, targetFolderId) {
        return this.makeRequest(CONFIG.API_ENDPOINTS.MOVE_FILE, {
            file_id: fileId,
            folder_id: targetFolderId
        });
    },

    moveFolder: function(folderId, targetFolderId) {
        return this.makeRequest(CONFIG.API_ENDPOINTS.MOVE_FOLDER, {
            folder_id: folderId,
            parent_id: targetFolderId
        });
    }
};

var NotificationManager = {
    showSuccess: function(message) {
        return Swal.fire({
            icon: 'success',
            title: 'Başarılı!',
            text: message,
            showConfirmButton: false,
            timer: 2000
        });
    },

    showError: function(message) {
        Swal.fire({
            icon: 'error',
            title: 'Hata!',
            text: message
        });
    },

    showConfirm: function(options) {
        Swal.fire({
            title: options.title,
            text: options.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet',
            cancelButtonText: 'İptal'
        }).then(function(result) {
            if (result.isConfirmed && options.callback) {
                options.callback();
            }
        });
    },

    showPrompt: function(options) {
        Swal.fire({
            title: options.title,
            input: options.input,
            inputValue: options.inputValue,
            showCancelButton: true,
            confirmButtonText: 'Değiştir',
            cancelButtonText: 'İptal',
            inputValidator: function(value) {
                if (!value.trim()) {
                    return 'Bu alan boş bırakılamaz!';
                }
            }
        }).then(function(result) {
            if (result.isConfirmed && options.callback) {
                options.callback(result.value);
            }
        });
    }
};

function FileManager(appState) {
    this.state = appState;
}

FileManager.prototype.moveFile = function(fileId, targetFolderId) {
    return APIManager.makeRequest(CONFIG.API_ENDPOINTS.MOVE_FILE, {
        file_id: fileId,
        folder_id: targetFolderId
    }).then(function(response) {
        if (response.status !== 'success') {
            throw new Error(response.message || 'Dosya taşınamadı.');
        }
        return response;
    });
};

FileManager.prototype.moveFolder = function(folderId, targetFolderId) {
    return APIManager.makeRequest(CONFIG.API_ENDPOINTS.MOVE_FOLDER, {
        folder_id: folderId,
        parent_id: targetFolderId
    }).then(function(response) {
        if (response.status !== 'success') {
            throw new Error(response.message || 'Klasör taşınamadı.');
        }
        return response;
    });
};

FileManager.prototype.renameFolder = function(folderId, newName) {
    return APIManager.renameFolder(folderId, newName).then(function(response) {
        if (response.status !== 'success') {
            throw new Error(response.message || 'Klasör adı değiştirilemedi.');
        }
        return response;
    });
};

FileManager.prototype.deleteFolder = function(folderId) {
    return APIManager.deleteFolder(folderId).then(function(response) {
        if (response.status !== 'success') {
            throw new Error(response.message || 'Klasör silinemedi.');
        }
        return response;
    });
};

function UIManager(appState) {
    this.state = appState;
    this.fileManager = new FileManager(appState);
    this.searchManager = new SearchManager(appState);
    this.initializeEventListeners();
}

UIManager.prototype.initializeSortOptions = function() {
    var self = this;
    var sortSelect = document.getElementById('sortOptions');
    if (sortSelect) {
        sortSelect.value = this.state.sortPreference;
        sortSelect.addEventListener('change', function() {
            var sort = this.value;
            localStorage.setItem('sortPreference', sort);
            self.state.updateUrl({ sort: sort });
        });
    }
};

UIManager.prototype.initializeSearchFilters = function() {
    var self = this;
    var searchInput = document.getElementById('searchFiles');
    var searchButton = document.getElementById('searchButton');
    var fileTypeSelect = document.getElementById('fileType');
    var dateFromInput = document.getElementById('dateFrom');
    var dateToInput = document.getElementById('dateTo');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            self.searchManager.handleSearch();
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', function() {
            self.searchManager.performSearch();
        });
    }

    [fileTypeSelect, dateFromInput, dateToInput].forEach(function(element) {
        if (element) {
            element.addEventListener('change', function() {
                self.searchManager.performSearch();
            });
        }
    });

    this.searchManager.loadFiltersFromUrl();
};

UIManager.prototype.initializeShareButtons = function() {
    var shareButtons = document.querySelectorAll('.share-btn');
    var shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
    var shareUrlInput = document.getElementById('shareUrl');
    var copyButton = document.getElementById('copyShareUrl');

    shareButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            shareUrlInput.value = this.getAttribute('data-share-url');
            shareModal.show();
        });
    });

    if (copyButton) {
        copyButton.addEventListener('click', function() {
            shareUrlInput.select();
            document.execCommand('copy');
            NotificationManager.showSuccess('Paylaşım linki panoya kopyalandı.');
        });
    }
};

UIManager.prototype.initializeFolderOperations = function() {
    var self = this;
    var createFolderModal = new bootstrap.Modal(document.getElementById('createFolderModal'));
    var createFolderForm = document.getElementById('createFolderForm');
    var createFolderBtn = document.getElementById('createFolderBtn');

    if (createFolderBtn) {
        createFolderBtn.addEventListener('click', function() {
            var folderName = document.getElementById('folderName').value;
            var folderDescription = document.getElementById('folderDescription').value;

            if (!folderName.trim()) {
                NotificationManager.showError('Lütfen bir klasör ismi girin!');
                return;
            }

            APIManager.createFolder(folderName, folderDescription, self.state.currentFolderId)
            .then(function(response) {
                if (response.status === 'success') {
                    createFolderModal.hide();
                    createFolderForm.reset();

                    NotificationManager.showSuccess('Klasör başarıyla oluşturuldu.').then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Bir hata oluştu.');
                }
            })
            .catch(function(error) {
                NotificationManager.showError(error.message);
            });
        });
    }

    document.querySelectorAll('.rename-folder').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var folderId = this.dataset.folderId;
            var currentName = this.dataset.folderName;
            
            NotificationManager.showPrompt({
                title: 'Klasör Adı Değiştir',
                input: 'text',
                inputValue: currentName,
                callback: function(newName) {
                    self.fileManager.renameFolder(folderId, newName)
                    .then(function() {
                        window.location.reload();
                    })
                    .catch(function(error) {
                        NotificationManager.showError(error.message);
                    });
                }
            });
        });
    });

    document.querySelectorAll('.delete-folder').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var folderId = this.dataset.folderId;
            var folderName = this.dataset.folderName;

            NotificationManager.showConfirm({
                title: 'Emin misin?',
                text: '"' + folderName + '" klasörünü içindekilerle beraber silmek istediğine emin misin?',
                callback: function() {
                    self.fileManager.deleteFolder(folderId)
                    .then(function() {
                        NotificationManager.showSuccess('Klasör başarıyla silindi.').then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(function(error) {
                        NotificationManager.showError(error.message);
                    });
                }
            });
        });
    });
};

UIManager.prototype.initializeDragAndDrop = function() {
    var self = this;
    var draggableItems = document.querySelectorAll('.file-card, .folder-card');
    var dropZones = document.querySelectorAll('.folder-card, .files-container');

    draggableItems.forEach(function(item) {
        item.setAttribute('draggable', true);
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });

    dropZones.forEach(function(zone) {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('dragleave', handleDragLeave);
        zone.addEventListener('drop', handleDrop);
    });
};

UIManager.prototype.initializeFileUpload = function() {
    var self = this;
    var filesContainer = document.querySelector('.files-container');
    var dragDropZone = document.getElementById('dragDropZone');
    var fileInput = document.getElementById('fileInput');
    var dragCounter = 0;

    if (!filesContainer || !dragDropZone || !fileInput) {
        console.error('Dosya yükleme için gerekli elementler bulunamadı');
        return;
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function handleDragEnter(e) {
        preventDefaults(e);
        dragCounter++;
        if (dragCounter === 1) {
            if (e.dataTransfer.types.includes('Files')) {
                dragDropZone.style.display = 'flex';
                dragDropZone.classList.add('drag-over');
            }
        }
    }

    function handleDragLeave(e) {
        preventDefaults(e);
        dragCounter--;
        if (dragCounter === 0) {
            dragDropZone.style.display = 'none';
            dragDropZone.classList.remove('drag-over');
        }
    }

    function handleDrop(e) {
        preventDefaults(e);
        dragCounter = 0;
        dragDropZone.style.display = 'none';
        dragDropZone.classList.remove('drag-over');
        
        if (e.dataTransfer.types.includes('Files')) {
            var files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFiles(files);
            }
        }
    }

    filesContainer.addEventListener('dragenter', handleDragEnter, false);
    filesContainer.addEventListener('dragover', function(e) {
        preventDefaults(e);
        if (e.dataTransfer.types.includes('Files')) {
            dragDropZone.style.display = 'flex';
            dragDropZone.classList.add('drag-over');
        }
    }, false);
    filesContainer.addEventListener('dragleave', handleDragLeave, false);
    filesContainer.addEventListener('drop', handleDrop, false);

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    dragDropZone.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            handleFiles(this.files);
        }
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            self.handleFileUpload(file, filesContainer);
        });
    }
};

UIManager.prototype.handleFileUpload = function(file, container) {
    var progressContainer = document.createElement('div');
    progressContainer.className = 'upload-progress';
    progressContainer.innerHTML = `
        <div class="progress-info">
            <span class="filename">${escapeHtml(file.name)}</span>
            <span class="percentage">0%</span>
        </div>
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
    `;

    container.insertBefore(progressContainer, document.getElementById('filesGrid'));

    var formData = new FormData();
    formData.append('file', file);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/upload-file.php', true);

    var globalProgress = document.getElementById('globalUploadProgress');
    globalProgress.style.display = 'block';
    var globalProgressBar = globalProgress.querySelector('.progress-bar');

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            var percentComplete = Math.round((e.loaded / e.total) * 100);
            progressContainer.querySelector('.progress-bar').style.width = percentComplete + '%';
            progressContainer.querySelector('.percentage').textContent = percentComplete + '%';
            globalProgressBar.style.width = percentComplete + '%';
        }
    };

    xhr.onload = function() {
        progressContainer.remove();
        globalProgress.style.display = 'none';
        globalProgressBar.style.width = '0%';
        
        try {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                NotificationManager.showSuccess(response.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(response.message || 'Dosya yükleme hatası');
            }
        } catch (error) {
            NotificationManager.showError(error.message);
        }
    };

    xhr.onerror = function() {
        progressContainer.remove();
        // Hata durumunda global progress bar'ı gizle
        globalProgress.style.display = 'none';
        globalProgressBar.style.width = '0%';
        NotificationManager.showError('Dosya yüklenirken bir hata oluştu');
    };

    xhr.send(formData);
};

UIManager.prototype.initializeEventListeners = function() {
    var self = this;
    document.addEventListener('DOMContentLoaded', function() {
        self.initializeViewOptions();
        self.initializeSortOptions();
        self.initializeSearchFilters();
        self.initializeShareButtons();
        self.initializeFolderOperations();
        self.initializeDragAndDrop();
        self.initializeDeleteButtons();
        self.initializeFileUpload();
        self.initializeFilePreview();
        initializeSelectionFeatures();
    });
};

UIManager.prototype.initializeDeleteButtons = function() {
    var self = this;
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var fileId = this.dataset.fileId;
            var fileName = this.dataset.fileName;

            NotificationManager.showConfirm({
                title: 'Dosyayı Sil',
                text: `"${fileName}" dosyasını silmek istediğinize emin misiniz?`,
                callback: function() {
                    fetch('/delete-file.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            file_id: fileId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            NotificationManager.showSuccess(data.message);
                            // Dosya kartını sayfadan kaldır
                            document.querySelector(`.file-card[data-file-id="${fileId}"]`).remove();
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        NotificationManager.showError(error.message);
                    });
                }
            });
        });
    });
};

UIManager.prototype.initializeFilePreview = function() {
    var previewModalEl = document.getElementById('previewModal');
    if (!previewModalEl) return;
    var previewModal = new bootstrap.Modal(previewModalEl);
    var previewContent = previewModalEl.querySelector('.preview-content');

    document.querySelector('.files-container').addEventListener('click', function(e) {
        var btn = e.target.closest('.preview-btn');
        if (!btn) return;

        var fileCard = btn.closest('.file-card');
        if (!fileCard) return;

        var fileId = fileCard.dataset.fileId;
        var fileName = fileCard.dataset.fileName;
        var mimeType = fileCard.dataset.mimeType;

        var downloadBtn = fileCard.querySelector('.btn-primary');
        var downloadHref = downloadBtn ? downloadBtn.getAttribute('href') : '';
        var shareToken = '';
        if (downloadHref && downloadHref.includes('token=')) {
            shareToken = downloadHref.split('token=')[1];
        }

        previewContent.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
        previewModal.show();

        if (mimeType && mimeType.startsWith('image/')) {
            previewContent.innerHTML = `
                <img src="/download.php?token=${shareToken}" class="img-fluid" alt="${fileName}">
            `;
        } else if (mimeType === 'application/pdf') {
            previewContent.innerHTML = `
                <embed src="/download.php?token=${shareToken}" type="application/pdf" width="100%" height="600px">
            `;
        } else if (mimeType && mimeType.startsWith('video/')) {
            previewContent.innerHTML = `
                <video controls class="w-100">
                    <source src="/download.php?token=${shareToken}" type="${mimeType}">
                    Tarayıcınız video oynatmayı desteklemiyor.
                </video>
            `;
        } else if (mimeType && mimeType.startsWith('audio/')) {
            previewContent.innerHTML = `
                <audio controls class="w-100">
                    <source src="/download.php?token=${shareToken}" type="${mimeType}">
                    Tarayıcınız ses oynatmayı desteklemiyor.
                </audio>
            `;
        } else if (mimeType && mimeType.startsWith('text/')) {
            fetch(`/download.php?token=${shareToken}`)
            .then(response => response.text())
            .then(text => {
                previewContent.innerHTML = `
                    <pre class="preview-text">${escapeHtml(text)}</pre>
                `;
            });
        } else {
            previewContent.innerHTML = `
                <div class="alert alert-info">
                    Bu dosya türü için önizleme yapılamıyor.
                    <br><br>
                    <a href="/download.php?token=${shareToken}" class="btn btn-primary">
                        <i class="fas fa-download"></i> İndir
                    </a>
                </div>
            `;
        }
    });
};

UIManager.prototype.initializeViewOptions = function() {
    var self = this;
    var viewButtons = document.querySelectorAll('[data-view]');
    viewButtons.forEach(function(btn) {
        if (btn.dataset.view === self.state.viewPreference) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }

        btn.addEventListener('click', function() {
            var view = this.dataset.view;
            viewButtons.forEach(function(b) {
                b.classList.remove('active');
            });
            this.classList.add('active');
            document.querySelector('.files-grid').classList.toggle('list-view', view === 'list');
            localStorage.setItem('viewPreference', view);
        });
    });
};

function SearchManager(appState) {
    this.state = appState;
}

SearchManager.prototype.handleSearch = function() {
    var self = this;
    clearTimeout(this.state.searchTimeout);
    this.state.searchTimeout = setTimeout(function() {
        self.performSearch();
    }, CONFIG.SEARCH_DELAY);
};

SearchManager.prototype.performSearch = function() {
    var searchTerm = document.getElementById('searchFiles').value.toLowerCase();
    var selectedTypes = Array.from(document.getElementById('fileType').selectedOptions)
    .map(function(option) {
        return option.value.split(',');
    })
    .reduce(function(acc, val) {
        return acc.concat(val);
    }, []);
    var dateFrom = document.getElementById('dateFrom').value;
    var dateTo = document.getElementById('dateTo').value;

    var params = {
        search: searchTerm || null,
        types: selectedTypes.length > 0 ? selectedTypes.join(',') : null,
        dateFrom: dateFrom || null,
        dateTo: dateTo || null
    };

    this.state.updateUrl(params);
};

SearchManager.prototype.loadFiltersFromUrl = function() {
    var urlParams = this.state.getUrlParams();
    
    if (urlParams.has('search')) {
        document.getElementById('searchFiles').value = urlParams.get('search');
    }
    
    if (urlParams.has('types')) {
        var types = urlParams.get('types').split(',');
        var fileTypeSelect = document.getElementById('fileType');
        types.forEach(function(type) {
            Array.from(fileTypeSelect.options).forEach(function(option) {
                if (option.value.split(',').some(function(t) { return types.includes(t); })) {
                    option.selected = true;
                }
            });
        });
    }
    
    if (urlParams.has('dateFrom')) {
        document.getElementById('dateFrom').value = urlParams.get('dateFrom');
    }
    if (urlParams.has('dateTo')) {
        document.getElementById('dateTo').value = urlParams.get('dateTo');
    }
};

var app = new AppState();
var ui = new UIManager(app);

document.addEventListener('DOMContentLoaded', function() {
    var draggableItems = document.querySelectorAll('.file-card, .folder-card');
    var dropZones = document.querySelectorAll('.folder-card, .files-container');

    draggableItems.forEach(function(item) {
        item.setAttribute('draggable', true);
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });

    dropZones.forEach(function(zone) {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('dragleave', handleDragLeave);
        zone.addEventListener('drop', handleDrop);
    });

    document.querySelectorAll('.rename-folder').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var folderId = this.dataset.folderId;
            var currentName = this.dataset.folderName;
            
            NotificationManager.showPrompt({
                title: 'Klasör Adı Değiştir',
                input: 'text',
                inputValue: currentName,
                callback: function(newName) {
                    ui.fileManager.renameFolder(folderId, newName)
                    .then(function() {
                        window.location.reload();
                    })
                    .catch(function(error) {
                        NotificationManager.showError(error.message);
                    });
                }
            });
        });
    });

    document.querySelectorAll('.delete-folder').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var folderId = this.dataset.folderId;
            var folderName = this.dataset.folderName;

            NotificationManager.showConfirm({
                title: 'Emin misin?',
                text: '"' + folderName + '" klasörünü ve içindeki tüm dosyaları silmek istediğinize emin misin?',
                callback: function() {
                    ui.fileManager.deleteFolder(folderId)
                    .then(function() {
                        window.location.reload();
                    })
                    .catch(function(error) {
                        NotificationManager.showError(error.message);
                    });
                }
            });
        });
    });

    initializeInfiniteScroll();
});

function handleDragStart(e) {
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', JSON.stringify({
        id: e.target.dataset.fileId || e.target.dataset.folderId,
        type: e.target.dataset.itemType
    }));
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    document.querySelectorAll('.drag-over').forEach(function(element) {
        element.classList.remove('drag-over');
    });
}

function handleDragOver(e) {
    e.preventDefault();
    if (!e.target.classList.contains('drag-over')) {
        e.target.classList.add('drag-over');
    }
}

function handleDragLeave(e) {
    e.target.classList.remove('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    e.target.classList.remove('drag-over');

    if (document.getElementById('dragDropZone').style.display === 'flex') {
        return;
    }

    var dropZone = e.target.closest('.folder-card');
    if (!dropZone) return;

    try {
        var dataTransfer = e.dataTransfer.getData('text/plain');
        if (!dataTransfer) return;

        var data = JSON.parse(dataTransfer);
        var targetFolderId = dropZone.dataset.folderId;

        if (data.type === 'file') {
            APIManager.moveFile(data.id, targetFolderId)
            .then(function(response) {
                if (response.status === 'success') {
                    NotificationManager.showSuccess('Dosya başarıyla taşındı!');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(response.message || 'Dosya taşıma işlemi başarısız');
                }
            })
            .catch(function(error) {
                console.error('Move file error:', error);
                NotificationManager.showError('Dosya taşınırken bir hata oluştu: ' + error.message);
            });
        } else if (data.type === 'folder') {
            APIManager.moveFolder(data.id, targetFolderId)
            .then(function(response) {
                if (response.status === 'success') {
                    NotificationManager.showSuccess('Klasör başarıyla taşındı!');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(response.message || 'Klasör taşıma işlemi başarısız');
                }
            })
            .catch(function(error) {
                NotificationManager.showError('Klasör taşınırken bir hata oluştu: ' + error.message);
            });
        }
    } catch (error) {
        console.error('Drop işlemi hatası:', error);
        NotificationManager.showError('İşlem sırasında bir hata oluştu');
    }
}

function addFolderPreview() {
    document.querySelectorAll('.folder-card').forEach(folder => {
        const folderId = folder.dataset.folderId;
        const preview = new bootstrap.Tooltip(folder, {
            title: 'Yükleniyor...',
            html: true,
            placement: 'auto',
            delay: { show: 500, hide: 100 }
        });

        folder.addEventListener('mouseenter', async () => {
            try {
                const response = await fetch(`/get-folder-preview.php?id=${folderId}`);
                const data = await response.json();
                const content = `
                    <div class="folder-preview">
                        <div>Dosyalar: ${data.fileCount}</div>
                        <div>Alt Klasörler: ${data.subfolderCount}</div>
                        <div>Son Güncelleme: ${data.lastUpdate}</div>
                    </div>
                `;
                preview._config.title = content;
                preview.update();
            } catch (error) {
                console.error('Önizleme yüklenemedi:', error);
            }
        });
    });
}

function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            document.querySelectorAll('.item-select').forEach(checkbox => {
                checkbox.checked = true;
            });
        }
        
        if (e.key === 'Delete' && document.querySelector('.item-select:checked')) {
            e.preventDefault();
            handleBulkDelete();
        }
        
        if (e.ctrlKey && e.key === 'c') {
            copySelectedLinks();
        }
    });
}

function initializeFilePreview() {
    document.querySelectorAll('.file-card').forEach(file => {
        file.addEventListener('dblclick', (e) => {
            const fileType = file.dataset.mimeType;
            const fileUrl = file.dataset.fileUrl;
            
            if (fileType.startsWith('image/')) {
                showImagePreview(fileUrl);
            } else if (fileType.startsWith('video/')) {
                showVideoPreview(fileUrl);
            } else if (fileType === 'application/pdf') {
                showPDFPreview(fileUrl);
            } else {
                window.location.href = `/download.php?token=${file.dataset.shareToken}`;
            }
        });
    });
}

function enhancedFiltering() {
    const filterOptions = {
        size: {
            small: size => size < 1024 * 1024, // 1MB'dan küçük
            medium: size => size >= 1024 * 1024 && size < 10 * 1024 * 1024, // 1MB-10MB
            large: size => size >= 10 * 1024 * 1024 // 10MB'dan büyük
        },
        date: {
            today: date => isToday(date),
            week: date => isThisWeek(date),
            month: date => isThisMonth(date)
        }
    };

    // Filtre kontrollerini ekleyin
    const filterControls = `
        <div class="filter-group">
            <label>Boyut</label>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" data-size="small">Küçük</button>
                <button class="btn btn-outline-secondary" data-size="medium">Orta</button>
                <button class="btn btn-outline-secondary" data-size="large">Büyük</button>
            </div>
        </div>
    `;
}

function enhancedDragAndDrop() {
    let draggedFiles = new Set();
    
    document.addEventListener('click', (e) => {
        if (e.target.matches('.file-card, .folder-card') && e.shiftKey) {
            const items = document.querySelectorAll('.file-card, .folder-card');
            const lastSelected = Array.from(items).find(item => item.classList.contains('selected'));
            if (lastSelected) {
                selectItemsBetween(lastSelected, e.target);
            }
        }
    });
}

function initializeAutoBackup() {
    setInterval(async () => {
        const changes = await checkForChanges();
        if (changes.length > 0) {
            NotificationManager.showInfo(`${changes.length} yeni değişiklik var. Yedeklemek ister misiniz?`);
        }
    }, 30 * 60 * 1000);
}

function initializeSelectionFeatures() {
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
            e.preventDefault();
            selectAllItems();
        }
    });

    document.querySelectorAll('.item-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionButtons();
        });
    });

    document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
        handleBulkDelete();
    });
}

function selectAllItems() {
    document.querySelectorAll('.item-select').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateBulkActionButtons();
}

function updateBulkActionButtons() {
    const selectedCount = document.querySelectorAll('.item-select:checked').length;
    const bulkActionButtons = document.getElementById('bulkActionButtons');
    
    if (selectedCount > 0) {
        bulkActionButtons.style.display = 'inline-flex';
        document.getElementById('deleteSelectedBtn').textContent = `Seçilenleri Sil (${selectedCount})`;
    } else {
        bulkActionButtons.style.display = 'none';
    }
}

function handleBulkDelete() {
    const selectedItems = document.querySelectorAll('.item-select:checked');
    const items = Array.from(selectedItems).map(checkbox => {
        const card = checkbox.closest('.file-card, .folder-card');
        return {
            id: card.dataset.fileId || card.dataset.folderId,
            type: card.dataset.itemType,
            name: card.dataset.fileName || card.querySelector('.folder-name').textContent.trim()
        };
    });

    if (items.length === 0) return;

    NotificationManager.showConfirm({
        title: 'Toplu Silme',
        text: `${items.length} öğeyi silmek istediğinize emin misiniz?`,
        callback: function() {
            const deletePromises = items.map(item => {
                if (item.type === 'file') {
                    return fetch('/delete-file.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            file_id: item.id
                        })
                    });
                } else {
                    return APIManager.deleteFolder(item.id);
                }
            });

            Promise.all(deletePromises)
            .then(() => {
                NotificationManager.showSuccess('Seçili öğeler başarıyla silindi');
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(error => {
                NotificationManager.showError('Bazı öğeler silinirken hata oluştu');
                console.error('Bulk delete error:', error);
            });
        }
    });
}