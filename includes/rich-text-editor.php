<?php
// Rich Text Editor için gerekli PHP değişkenleri
if (!defined('BASE_URL')) {
    $current_dir = basename(dirname($_SERVER['PHP_SELF']));
    $page_level = ($current_dir === 'pages' || $current_dir === 'admin') ? '../' : '';
    define('BASE_URL', $page_level);
}
if (!function_exists('generateCSRFToken')) {
    require_once(__DIR__ . '/functions.php');
}
$rte_base_url = BASE_URL;
$rte_csrf_token = generateCSRFToken();
?>
<!-- Rich Text Editor CSS -->
<style>
    .rte-toolbar {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-bottom: none;
        border-radius: 5px 5px 0 0;
        padding: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
    }
    .rte-btn-group {
        display: flex;
        gap: 2px;
        border-right: 1px solid #ddd;
        padding-right: 8px;
        margin-right: 8px;
    }
    .rte-btn-group:last-child {
        border-right: none;
        margin-right: 0;
        padding-right: 0;
    }
    .rte-btn {
        background: white;
        border: 1px solid #ddd;
        border-radius: 3px;
        padding: 6px 10px;
        cursor: pointer;
        font-size: 14px;
        color: #333;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
    }
    .rte-btn:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }
    .rte-btn.active {
        background: var(--primary-color, #003366);
        color: white;
        border-color: var(--primary-color, #003366);
    }
    .rte-btn i {
        font-size: 14px;
    }
    .rte-editor {
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 5px 5px;
        min-height: 300px;
        padding: 15px;
        font-size: 14px;
        line-height: 1.6;
        font-family: inherit;
        background: white;
        outline: none;
        overflow-y: auto;
    }
    .rte-editor:focus {
        border-color: var(--primary-color, #003366);
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
    }
    .rte-editor[contenteditable="true"]:empty:before {
        content: attr(placeholder);
        color: #999;
        font-style: italic;
    }
    .rte-wrapper {
        margin-bottom: 10px;
    }
    .rte-select {
        padding: 4px 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 13px;
        background: white;
        cursor: pointer;
    }
    /* Link Modal Stilleri */
    .rte-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }
    .rte-modal.active {
        display: flex;
    }
    .rte-modal-content {
        background-color: white;
        padding: 25px;
        border-radius: 10px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
    .rte-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e0e0e0;
    }
    .rte-modal-header h3 {
        margin: 0;
        color: var(--primary-color, #003366);
    }
    .rte-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rte-modal-close:hover {
        color: #333;
    }
    .rte-link-type-selector {
        margin-bottom: 20px;
    }
    .rte-link-type-selector label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: #333;
    }
    .rte-link-type-options {
        display: flex;
        gap: 15px;
    }
    .rte-link-type-option {
        flex: 1;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s;
    }
    .rte-link-type-option:hover {
        border-color: var(--primary-color, #003366);
        background-color: #f0f7ff;
    }
    .rte-link-type-option.active {
        border-color: var(--primary-color, #003366);
        background-color: var(--primary-color, #003366);
        color: white;
    }
    .rte-link-form-group {
        margin-bottom: 20px;
    }
    .rte-link-form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    .rte-link-form-group input,
    .rte-link-form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .rte-link-form-group input:focus,
    .rte-link-form-group select:focus {
        outline: none;
        border-color: var(--primary-color, #003366);
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
    }
    .rte-file-list {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
    }
    .rte-file-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .rte-file-item:last-child {
        border-bottom: none;
    }
    .rte-file-item:hover {
        background-color: #f8f9fa;
    }
    .rte-file-item.selected {
        background-color: #e3f2fd;
        border-left: 3px solid var(--primary-color, #003366);
    }
    .rte-file-name {
        flex: 1;
        font-weight: 500;
    }
    .rte-file-size {
        color: #666;
        font-size: 12px;
        margin-left: 10px;
    }
    .rte-file-delete {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 3px;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
        margin-left: 10px;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rte-file-delete:hover {
        background-color: #c82333;
    }
    .rte-file-delete:active {
        background-color: #bd2130;
    }
    .rte-modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #e0e0e0;
    }
    .rte-modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }
    .rte-modal-btn-primary {
        background-color: var(--primary-color, #003366);
        color: white;
    }
    .rte-modal-btn-primary:hover {
        background-color: #002244;
    }
    .rte-modal-btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    .rte-modal-btn-secondary:hover {
        background-color: #5a6268;
    }
    .rte-upload-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #e0e0e0;
    }
    .rte-upload-btn {
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .rte-upload-btn:hover {
        background-color: #218838;
    }
    .rte-upload-input {
        display: none;
    }
    .rte-upload-status {
        margin-top: 10px;
        padding: 10px;
        border-radius: 5px;
        display: none;
    }
    .rte-upload-status.success {
        background-color: #d4edda;
        color: #155724;
        display: block;
    }
    .rte-upload-status.error {
        background-color: #f8d7da;
        color: #721c24;
        display: block;
    }
</style>

<!-- Rich Text Editor JavaScript -->
<script>
function initRichTextEditor(textareaId) {
    var textarea = document.getElementById(textareaId);
    if (!textarea) {
        console.error('Textarea bulunamadı: ' + textareaId);
        return;
    }
    
    // Textarea'yı gizle
    textarea.style.display = 'none';
    
    // Editor wrapper oluştur
    var wrapper = document.createElement('div');
    wrapper.className = 'rte-wrapper';
    
    // Toolbar oluştur
    var toolbar = document.createElement('div');
    toolbar.className = 'rte-toolbar';
    
    // Editor div oluştur
    var editor = document.createElement('div');
    editor.className = 'rte-editor';
    editor.contentEditable = 'true';
    editor.placeholder = textarea.placeholder || 'İçeriğinizi buraya yazın...';
    editor.innerHTML = textarea.value;
    
    // Link Modal oluştur
    var linkModal = createLinkModal();
    document.body.appendChild(linkModal);
    
    // Toolbar butonları
    var buttons = [
        { icon: 'bold', cmd: 'bold', title: 'Kalın' },
        { icon: 'italic', cmd: 'italic', title: 'İtalik' },
        { icon: 'underline', cmd: 'underline', title: 'Altı Çizili' },
        { separator: true },
        { icon: 'list-ul', cmd: 'insertUnorderedList', title: 'Sırasız Liste' },
        { icon: 'list-ol', cmd: 'insertOrderedList', title: 'Sıralı Liste' },
        { separator: true },
        { icon: 'align-left', cmd: 'justifyLeft', title: 'Sola Hizala' },
        { icon: 'align-center', cmd: 'justifyCenter', title: 'Ortala' },
        { icon: 'align-right', cmd: 'justifyRight', title: 'Sağa Hizala' },
        { separator: true },
        { icon: 'link', cmd: 'customLink', title: 'Link Ekle', custom: true },
        { icon: 'unlink', cmd: 'customUnlink', title: 'Link Kaldır', custom: true },
        { separator: true },
        { icon: 'undo', cmd: 'undo', title: 'Geri Al' },
        { icon: 'redo', cmd: 'redo', title: 'Yinele' },
        { separator: true },
        { icon: 'remove-format', cmd: 'removeFormat', title: 'Biçimlendirmeyi Kaldır' }
    ];
    
    var currentGroup = document.createElement('div');
    currentGroup.className = 'rte-btn-group';
    
    buttons.forEach(function(btn) {
        if (btn.separator) {
            toolbar.appendChild(currentGroup);
            currentGroup = document.createElement('div');
            currentGroup.className = 'rte-btn-group';
        } else {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'rte-btn';
            button.title = btn.title;
            button.innerHTML = '<i class="fas fa-' + btn.icon + '"></i>';
            if (btn.custom && btn.cmd === 'customLink') {
                button.onclick = function(e) {
                    e.preventDefault();
                    // Önce seçimi kaydet, sonra modal'ı aç
                    saveSelection();
                    setTimeout(function() {
                        openLinkModal();
                    }, 20);
                };
            } else if (btn.custom && btn.cmd === 'customUnlink') {
                button.onclick = function(e) {
                    e.preventDefault();
                    removeLink();
                };
            } else {
                button.onclick = function(e) {
                    e.preventDefault();
                    document.execCommand(btn.cmd, false, null);
                    editor.focus();
                    updateTextarea();
                };
            }
            currentGroup.appendChild(button);
        }
    });
    
    if (currentGroup.children.length > 0) {
        toolbar.appendChild(currentGroup);
    }
    
    // Başlık seçici
    var formatGroup = document.createElement('div');
    formatGroup.className = 'rte-btn-group';
    var formatSelect = document.createElement('select');
    formatSelect.className = 'rte-select';
    formatSelect.innerHTML = '<option value="">Paragraf</option><option value="h1">Başlık 1</option><option value="h2">Başlık 2</option><option value="h3">Başlık 3</option>';
    formatSelect.onchange = function() {
        if (this.value) {
            document.execCommand('formatBlock', false, '<' + this.value + '>');
        } else {
            document.execCommand('formatBlock', false, '<p>');
        }
        editor.focus();
        updateTextarea();
    };
    formatGroup.appendChild(formatSelect);
    toolbar.insertBefore(formatGroup, toolbar.firstChild);
    
    // Renk seçici
    var colorGroup = document.createElement('div');
    colorGroup.className = 'rte-btn-group';
    var colorInput = document.createElement('input');
    colorInput.type = 'color';
    colorInput.className = 'rte-select';
    colorInput.style.width = '40px';
    colorInput.style.height = '32px';
    colorInput.style.padding = '2px';
    colorInput.onchange = function() {
        document.execCommand('foreColor', false, this.value);
        editor.focus();
        updateTextarea();
    };
    colorGroup.appendChild(colorInput);
    toolbar.appendChild(colorGroup);
    
    // Link Modal oluşturma fonksiyonu
    function createLinkModal() {
        var modal = document.createElement('div');
        modal.className = 'rte-modal';
        modal.id = 'rte-link-modal';
        
        var modalContent = document.createElement('div');
        modalContent.className = 'rte-modal-content';
        
        var header = document.createElement('div');
        header.className = 'rte-modal-header';
        header.innerHTML = '<h3><i class="fas fa-link"></i> Link Ekle</h3><button class="rte-modal-close" onclick="closeLinkModal()"><i class="fas fa-times"></i></button>';
        
        var linkTypeSelector = document.createElement('div');
        linkTypeSelector.className = 'rte-link-type-selector';
        linkTypeSelector.innerHTML = '<label>Link Tipi:</label><div class="rte-link-type-options"><div class="rte-link-type-option active" data-type="internal"><i class="fas fa-folder"></i><br>Internal (Yüklenen Dosya)</div><div class="rte-link-type-option" data-type="external"><i class="fas fa-external-link-alt"></i><br>External (Harici Link)</div></div>';
        
        var internalGroup = document.createElement('div');
        internalGroup.className = 'rte-link-form-group';
        internalGroup.id = 'rte-internal-group';
        internalGroup.innerHTML = '<label>Yüklenen Dosyayı Seç:</label><div class="rte-file-list" id="rte-file-list"><div style="padding: 20px; text-align: center; color: #999;">Dosyalar yükleniyor...</div></div>';
        
        var externalGroup = document.createElement('div');
        externalGroup.className = 'rte-link-form-group';
        externalGroup.id = 'rte-external-group';
        externalGroup.style.display = 'none';
        externalGroup.innerHTML = '<label>Link URL:</label><input type="url" id="rte-external-url" placeholder="https://example.com" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"><label style="margin-top: 10px;">Link Metni (Opsiyonel):</label><input type="text" id="rte-external-text" placeholder="Link metni" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">';
        
        var uploadSection = document.createElement('div');
        uploadSection.className = 'rte-upload-section';
        uploadSection.innerHTML = '<button type="button" class="rte-upload-btn" onclick="document.getElementById(\'rte-file-upload\').click()"><i class="fas fa-upload"></i> Dosya Yükle</button><input type="file" id="rte-file-upload" class="rte-upload-input" onchange="handleFileUpload(event)"><div class="rte-upload-status" id="rte-upload-status"></div>';
        
        var buttons = document.createElement('div');
        buttons.className = 'rte-modal-buttons';
        buttons.innerHTML = '<button class="rte-modal-btn rte-modal-btn-secondary" onclick="closeLinkModal()">İptal</button><button class="rte-modal-btn rte-modal-btn-primary" onclick="insertLink()">Link Ekle</button>';
        
        modalContent.appendChild(header);
        modalContent.appendChild(linkTypeSelector);
        modalContent.appendChild(internalGroup);
        modalContent.appendChild(externalGroup);
        modalContent.appendChild(uploadSection);
        modalContent.appendChild(buttons);
        modal.appendChild(modalContent);
        
        // Link tipi seçimi
        var typeOptions = linkTypeSelector.querySelectorAll('.rte-link-type-option');
        typeOptions.forEach(function(option) {
            option.addEventListener('click', function() {
                typeOptions.forEach(function(opt) { opt.classList.remove('active'); });
                this.classList.add('active');
                var type = this.getAttribute('data-type');
                if (type === 'internal') {
                    document.getElementById('rte-internal-group').style.display = 'block';
                    document.getElementById('rte-external-group').style.display = 'none';
                    uploadSection.style.display = 'block'; // Dosya yükleme bölümünü göster
                    loadFileList();
                } else {
                    document.getElementById('rte-internal-group').style.display = 'none';
                    document.getElementById('rte-external-group').style.display = 'block';
                    uploadSection.style.display = 'none'; // Dosya yükleme bölümünü gizle
                }
            });
        });
        
        // Modal dışına tıklanınca kapat
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeLinkModal();
            }
        });
        
        return modal;
    }
    
    // Seçili metni ve imleç konumunu sakla
    var savedRange = null;
    var savedSelectedText = '';
    
    // Seçimi kaydet (butona tıklanınca çağrılır)
    function saveSelection() {
        // Editor'a odaklan
        editor.focus();
        
        // Kısa bir gecikme ile seçimi kaydet (focus işleminin tamamlanması için)
        setTimeout(function() {
            var sel = window.getSelection();
            savedSelectedText = '';
            savedRange = null;
            
            if (sel.rangeCount > 0) {
                var range = sel.getRangeAt(0);
                var container = range.commonAncestorContainer;
                
                // Seçim editor içinde mi kontrol et
                if (editor.contains(container) || container === editor || 
                    (container.nodeType === 3 && editor.contains(container.parentNode))) {
                    try {
                        savedRange = range.cloneRange();
                        savedSelectedText = range.toString();
                    } catch (e) {
                        // Range kopyalanamazsa, yeni bir range oluştur
                        try {
                            savedRange = document.createRange();
                            savedRange.setStart(range.startContainer, range.startOffset);
                            savedRange.setEnd(range.endContainer, range.endOffset);
                            savedSelectedText = savedRange.toString();
                        } catch (e2) {
                            savedRange = null;
                        }
                    }
                }
            }
            
            // Eğer seçim yoksa veya editor dışındaysa, imleç konumunu kaydet
            if (!savedRange) {
                try {
                    var range = document.createRange();
                    // Editor içindeki mevcut seçimi kontrol et
                    var sel = window.getSelection();
                    if (sel.rangeCount > 0) {
                        var currentRange = sel.getRangeAt(0);
                        var container = currentRange.commonAncestorContainer;
                        if (editor.contains(container) || container === editor) {
                            range.setStart(currentRange.startContainer, currentRange.startOffset);
                            range.setEnd(currentRange.startContainer, currentRange.startOffset);
                        } else {
                            range.selectNodeContents(editor);
                            range.collapse(false);
                        }
                    } else {
                        range.selectNodeContents(editor);
                        range.collapse(false);
                    }
                    savedRange = range;
                } catch (e) {
                    savedRange = null;
                }
            }
        }, 10);
    }
    
    // Link modal'ı aç
    function openLinkModal() {
        // Seçimi kaydet (butona tıklanınca zaten çağrılmış olmalı ama yine de kontrol edelim)
        if (!savedRange) {
            saveSelection();
        }
        
        var modal = document.getElementById('rte-link-modal');
        if (modal) {
            modal.classList.add('active');
            // Internal seçili olduğu için dosya listesini yükle
            loadFileList();
        }
    }
    
    // Link modal'ı kapat
    window.closeLinkModal = function() {
        var modal = document.getElementById('rte-link-modal');
        if (modal) {
            modal.classList.remove('active');
            // Formu temizle
            document.getElementById('rte-external-url').value = '';
            document.getElementById('rte-external-text').value = '';
            var fileList = document.getElementById('rte-file-list');
            if (fileList) {
                var items = fileList.querySelectorAll('.rte-file-item');
                items.forEach(function(item) { item.classList.remove('selected'); });
            }
            // Seçimi temizle (opsiyonel - kullanıcı iptal ederse)
            // savedRange = null;
            // savedSelectedText = '';
        }
    };
    
    // Dosya listesini yükle
    function loadFileList() {
        var fileList = document.getElementById('rte-file-list');
        fileList.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Dosyalar yükleniyor...</div>';
        
        fetch('<?php echo $rte_base_url; ?>admin/list-files.php')
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success && data.files && data.files.length > 0) {
                    fileList.innerHTML = '';
                    data.files.forEach(function(file) {
                        var item = document.createElement('div');
                        item.className = 'rte-file-item';
                        item.setAttribute('data-url', file.url);
                        item.setAttribute('data-name', file.name);
                        var sizeText = formatFileSize(file.size);
                        
                        // Silme butonu ekle
                        var deleteBtn = document.createElement('button');
                        deleteBtn.className = 'rte-file-delete';
                        deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                        deleteBtn.title = 'Dosyayı Sil';
                        deleteBtn.onclick = function(e) {
                            e.stopPropagation(); // Seçim olayını engelle
                            if (confirm('Bu dosyayı silmek istediğinizden emin misiniz?')) {
                                deleteFile(file.name);
                            }
                        };
                        
                        var nameSpan = document.createElement('span');
                        nameSpan.className = 'rte-file-name';
                        nameSpan.textContent = file.name;
                        nameSpan.style.cursor = 'pointer';
                        nameSpan.style.flex = '1';
                        
                        var sizeSpan = document.createElement('span');
                        sizeSpan.className = 'rte-file-size';
                        sizeSpan.textContent = sizeText;
                        
                        item.appendChild(nameSpan);
                        item.appendChild(sizeSpan);
                        item.appendChild(deleteBtn);
                        
                        // Dosya seçimi için tıklama
                        nameSpan.addEventListener('click', function(e) {
                            e.stopPropagation();
                            var items = fileList.querySelectorAll('.rte-file-item');
                            items.forEach(function(it) { it.classList.remove('selected'); });
                            item.classList.add('selected');
                        });
                        
                        fileList.appendChild(item);
                    });
                } else {
                    fileList.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Henüz dosya yüklenmemiş.</div>';
                }
            })
            .catch(function(error) {
                fileList.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;">Dosyalar yüklenirken hata oluştu.</div>';
                console.error('Error:', error);
            });
    }
    
    // Dosya boyutunu formatla
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
    
    // Dosya silme fonksiyonu
    function deleteFile(fileName) {
        // CSRF token'ı formdan al
        var csrfToken = '<?php echo $rte_csrf_token; ?>';
        var form = document.querySelector('form');
        if (form) {
            var formToken = form.querySelector('input[name="csrf_token"]');
            if (formToken) {
                csrfToken = formToken.value;
            }
        }
        
        var formData = new FormData();
        formData.append('file_name', fileName);
        formData.append('csrf_token', csrfToken);
        
        fetch('<?php echo $rte_base_url; ?>admin/delete-file.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                // Dosya listesini yenile
                loadFileList();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(function(error) {
            alert('Dosya silinirken hata oluştu.');
            console.error('Error:', error);
        });
    }
    
    // Dosya yükleme
    window.handleFileUpload = function(event) {
        var file = event.target.files[0];
        if (!file) return;
        
        // CSRF token'ı formdan veya hidden input'tan al
        var csrfToken = '<?php echo $rte_csrf_token; ?>';
        var form = document.querySelector('form');
        if (form) {
            var formToken = form.querySelector('input[name="csrf_token"]');
            if (formToken) {
                csrfToken = formToken.value;
            }
        }
        
        var formData = new FormData();
        formData.append('file', file);
        formData.append('csrf_token', csrfToken);
        
        var statusDiv = document.getElementById('rte-upload-status');
        statusDiv.className = 'rte-upload-status';
        statusDiv.style.display = 'block';
        statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Dosya yükleniyor...';
        
        fetch('<?php echo $rte_base_url; ?>admin/upload-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                statusDiv.className = 'rte-upload-status success';
                statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                // Dosya listesini yenile
                setTimeout(function() {
                    loadFileList();
                    statusDiv.style.display = 'none';
                }, 2000);
            } else {
                statusDiv.className = 'rte-upload-status error';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
            }
            // Input'u temizle
            event.target.value = '';
        })
        .catch(function(error) {
            statusDiv.className = 'rte-upload-status error';
            statusDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Dosya yükleme başarısız.';
            console.error('Error:', error);
            event.target.value = '';
        });
    };
    
    // Link ekle
    window.insertLink = function() {
        var modal = document.getElementById('rte-link-modal');
        var activeType = modal.querySelector('.rte-link-type-option.active').getAttribute('data-type');
        var url = '';
        var text = '';
        
        if (activeType === 'internal') {
            var selectedFile = modal.querySelector('.rte-file-item.selected');
            if (!selectedFile) {
                alert('Lütfen bir dosya seçin.');
                return;
            }
            url = selectedFile.getAttribute('data-url');
            text = selectedFile.getAttribute('data-name');
        } else {
            url = document.getElementById('rte-external-url').value.trim();
            if (!url) {
                alert('Lütfen bir URL girin.');
                return;
            }
            text = document.getElementById('rte-external-text').value.trim() || url;
        }
        
        // Editor'a odaklan
        editor.focus();
        
        // Kaydedilen seçimi kullan
        var hasSelectedText = savedSelectedText && savedSelectedText.trim().length > 0;
        var range = savedRange;
        
        if (!range) {
            // Range yoksa, editor'un sonuna git
            range = document.createRange();
            range.selectNodeContents(editor);
            range.collapse(false);
        }
        
        // Seçimi geri yükle
        try {
            var selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        } catch (e) {
            // Range geçersizse, yeni bir range oluştur
            range = document.createRange();
            range.selectNodeContents(editor);
            range.collapse(false);
            var selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        }
        
        // Link ekleme işlemini yap
        if (hasSelectedText && savedSelectedText.trim().length > 0) {
            // DURUM 1: Seçili metin var - Seçili metni linke çevir
            try {
                // Mevcut seçili metni al
                var textToLink = savedSelectedText;
                
                // Range'i düzenle - seçili metni sil
                range.deleteContents();
                
                // Link elementi oluştur
                var linkElement = document.createElement('a');
                linkElement.href = url;
                linkElement.target = '_blank';
                linkElement.textContent = textToLink;
                
                // Link'i ekle
                range.insertNode(linkElement);
                
                // İmleci link'in sonuna taşı
                var newRange = document.createRange();
                newRange.setStartAfter(linkElement);
                newRange.collapse(true);
                var selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(newRange);
            } catch (e) {
                // Alternatif yöntem: execCommand kullan
                try {
                    // Önce seçimi geri yükle
                    var selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                    document.execCommand('createLink', false, url);
                    // Link'e target="_blank" ekle
                    var links = editor.querySelectorAll('a');
                    if (links.length > 0) {
                        var lastLink = links[links.length - 1];
                        lastLink.setAttribute('target', '_blank');
                    }
                } catch (e2) {
                    // Son çare: insertHTML
                    var link = '<a href="' + url + '" target="_blank">' + savedSelectedText + '</a>';
                    document.execCommand('insertHTML', false, link);
                }
            }
        } else {
            // DURUM 2: Seçili metin yok - İmleç konumuna link ekle
            try {
                var linkElement = document.createElement('a');
                linkElement.href = url;
                linkElement.target = '_blank';
                linkElement.textContent = text;
                
                // Range'i kullanarak link'i ekle
                range.insertNode(linkElement);
                
                // İmleci link'in sonuna taşı
                var newRange = document.createRange();
                newRange.setStartAfter(linkElement);
                newRange.collapse(true);
                var selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(newRange);
            } catch (e) {
                // Alternatif yöntem: insertHTML
                var link = '<a href="' + url + '" target="_blank">' + text + '</a>';
                document.execCommand('insertHTML', false, link);
            }
        }
        
        // Seçimi temizle
        savedRange = null;
        savedSelectedText = '';
        
        editor.focus();
        updateTextarea();
        closeLinkModal();
    };
    
    // Link kaldırma fonksiyonu
    function removeLink() {
        editor.focus();
        
        var selection = window.getSelection();
        if (selection.rangeCount === 0) {
            alert('Lütfen kaldırmak istediğiniz linki seçin.');
            return;
        }
        
        var range = selection.getRangeAt(0);
        var selectedNode = range.commonAncestorContainer;
        
        // Seçili node bir link mi kontrol et
        var linkElement = null;
        
        // Eğer seçili node bir link ise
        if (selectedNode.nodeType === 1 && selectedNode.tagName === 'A') {
            linkElement = selectedNode;
        } else if (selectedNode.nodeType === 3) {
            // Text node ise, parent'ı kontrol et
            var parent = selectedNode.parentNode;
            if (parent && parent.tagName === 'A') {
                linkElement = parent;
            }
        } else {
            // Seçili node'un içinde link var mı kontrol et
            if (selectedNode.nodeType === 1) {
                linkElement = selectedNode.querySelector('a');
            } else {
                var parent = selectedNode.parentNode;
                if (parent) {
                    if (parent.tagName === 'A') {
                        linkElement = parent;
                    } else {
                        linkElement = parent.querySelector('a');
                    }
                }
            }
        }
        
        // Range içindeki tüm linkleri bul
        if (!linkElement) {
            try {
                var container = range.commonAncestorContainer;
                if (container.nodeType === 1) {
                    var links = container.querySelectorAll('a');
                    if (links.length > 0) {
                        linkElement = links[0];
                    }
                }
            } catch (e) {
                // Hata durumunda execCommand dene
                try {
                    document.execCommand('unlink', false, null);
                    updateTextarea();
                    return;
                } catch (e2) {
                    alert('Link kaldırılamadı. Lütfen linki seçip tekrar deneyin.');
                    return;
                }
            }
        }
        
        if (linkElement) {
            // Link'in içeriğini al
            var linkText = linkElement.textContent || linkElement.innerText;
            
            // Link'i kaldır ve içeriğini yerine koy
            var textNode = document.createTextNode(linkText);
            linkElement.parentNode.replaceChild(textNode, linkElement);
            
            // Seçimi güncelle
            range = document.createRange();
            range.selectNodeContents(textNode);
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
            
            updateTextarea();
        } else {
            // Link bulunamadı, execCommand dene
            try {
                document.execCommand('unlink', false, null);
                updateTextarea();
            } catch (e) {
                alert('Link bulunamadı. Lütfen linki seçip tekrar deneyin.');
            }
        }
    }
    
    // Textarea'yı güncelleme fonksiyonu
    function updateTextarea() {
        textarea.value = editor.innerHTML;
    }
    
    // Link'leri temizleme fonksiyonu (yapıştırma ve yazma için)
    function cleanPastedContent() {
        // Yapıştırılan içerikteki link'leri temizle
        var links = editor.querySelectorAll('a');
        links.forEach(function(link) {
            var text = link.textContent || link.innerText;
            var textNode = document.createTextNode(text);
            link.parentNode.replaceChild(textNode, link);
        });
    }
    
    // Silme işlemlerini dinle (delete, backspace)
    editor.addEventListener('keydown', function(e) {
        // Delete veya Backspace tuşları
        if (e.key === 'Delete' || e.key === 'Backspace') {
            var selection = window.getSelection();
            if (selection.rangeCount > 0) {
                var range = selection.getRangeAt(0);
                var container = range.commonAncestorContainer;
                
                // Seçili içerikte link var mı kontrol et
                var linkElement = null;
                
                if (container.nodeType === 1 && container.tagName === 'A') {
                    linkElement = container;
                } else if (container.nodeType === 3) {
                    var parent = container.parentNode;
                    if (parent && parent.tagName === 'A') {
                        linkElement = parent;
                    }
                }
                
                // Eğer link içindeyse veya link seçiliyse, link'i temizle
                if (linkElement) {
                    setTimeout(function() {
                        // Silme işleminden sonra link'i kontrol et
                        var remainingLinks = editor.querySelectorAll('a');
                        remainingLinks.forEach(function(link) {
                            // Eğer link boşsa veya sadece boşluk içeriyorsa kaldır
                            var linkText = (link.textContent || link.innerText || '').trim();
                            if (linkText === '') {
                                var textNode = document.createTextNode('');
                                link.parentNode.replaceChild(textNode, link);
                            }
                        });
                        updateTextarea();
                    }, 10);
                }
            }
        }
    });
    
    // Yapıştırma işlemini dinle
    editor.onpaste = function(e) {
        // Yapıştırma işlemini geçici olarak durdur
        e.preventDefault();
        
        // Yapıştırılan içeriği al (sadece metin)
        var pastedText = '';
        if (e.clipboardData && e.clipboardData.getData) {
            pastedText = e.clipboardData.getData('text/plain');
        } else if (window.clipboardData && window.clipboardData.getData) {
            pastedText = window.clipboardData.getData('Text');
        }
        
        // Sadece metin olarak yapıştır (link'ler olmadan)
        var selection = window.getSelection();
        if (selection.rangeCount > 0) {
            var range = selection.getRangeAt(0);
            range.deleteContents();
            
            // Metin node'u oluştur ve yapıştır
            var textNode = document.createTextNode(pastedText);
            range.insertNode(textNode);
            
            // İmleci yapıştırılan metnin sonuna taşı
            range.setStartAfter(textNode);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
        }
        
        updateTextarea();
    };
    
    // Boş link'leri temizleme fonksiyonu
    function cleanEmptyLinks() {
        var links = editor.querySelectorAll('a');
        var hasChanges = false;
        links.forEach(function(link) {
            // Eğer link boşsa veya sadece boşluk içeriyorsa kaldır
            var linkText = (link.textContent || link.innerText || '').trim();
            if (linkText === '') {
                var textNode = document.createTextNode('');
                if (link.parentNode) {
                    link.parentNode.replaceChild(textNode, link);
                    hasChanges = true;
                }
            }
        });
        if (hasChanges) {
            updateTextarea();
        }
    }
    
    // Editor değişikliklerini dinle
    editor.oninput = function() {
        // Boş link'leri temizle
        cleanEmptyLinks();
        updateTextarea();
    };
    
    editor.onkeyup = function() {
        // Silme işlemlerinden sonra boş link'leri temizle
        cleanEmptyLinks();
        updateTextarea();
    };
    
    // Wrapper'a ekle
    wrapper.appendChild(toolbar);
    wrapper.appendChild(editor);
    
    // Textarea'nın yanına ekle
    textarea.parentNode.insertBefore(wrapper, textarea);
    
    // Form submit'te güncelle
    var form = textarea.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            updateTextarea();
        });
    }
    
    return {
        getContent: function() {
            return editor.innerHTML;
        },
        setContent: function(content) {
            editor.innerHTML = content;
            updateTextarea();
        }
    };
}

// Sayfa yüklendiğinde otomatik başlat
document.addEventListener('DOMContentLoaded', function() {
    // #icerik textarea'sını bul ve başlat
    if (document.getElementById('icerik')) {
        initRichTextEditor('icerik');
    }
});
</script>

