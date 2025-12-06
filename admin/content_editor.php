<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Website Content Editor';

// Fetch existing content sections
global $mysqli;
$result = $mysqli->query("SELECT * FROM homepage_content WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
$sections = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
} else {
    $sections = [];
    $error = "Could not load content sections: " . $mysqli->error;
}

error_log('[Admin] Website content editor loaded: ' . count($sections) . ' active sections');

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Website Content Editor</h2>
            <p class="text-muted mb-0">Edit your homepage text, images, and layout formatting</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-eye me-2"></i>Preview Website
                </a>
                <a href="homepage_editor.php" class="btn btn-outline-secondary">
                    <i class="fas fa-cogs me-2"></i>Advanced Editor
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>Content updated successfully! 
        <a href="../index.php" target="_blank" class="alert-link">View changes on website</a>
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Content Sections -->
<div class="row">
    <div class="col-12">
        
        <?php if (empty($sections)): ?>
            <!-- No Content Message -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h4>No Content Sections Found</h4>
                    <p class="text-muted mb-4">It looks like your website content hasn't been set up yet.</p>
                    <a href="setup_homepage_cms.php" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Setup Website Content
                    </a>
                </div>
            </div>
        <?php else: ?>
            
            <!-- Content Editing Forms -->
            <?php foreach ($sections as $index => $section): ?>
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-<?= e(getSectionIcon($section['section_name'])) ?> me-2 text-primary"></i>
                                <?= e(ucwords(str_replace('_', ' ', $section['section_name']))) ?> Section
                            </h5>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-<?= e($section['is_active'] ? 'success' : 'secondary') ?> me-2">
                                    <?= e($section['is_active'] ? 'Active' : 'Inactive') ?>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSection(<?= e($index) ?>)">
                                    <i class="fas fa-chevron-down" id="toggle-icon-<?= e($index) ?>"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body" id="section-<?= e($index) ?>" style="display: <?= e($index === 0 ? 'block' : 'none') ?>;">
                        <form method="POST" action="save_content.php" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e($section['id']) ?>">
                            <input type="hidden" name="section_name" value="<?= e($section['section_name']) ?>">
                            
                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">Section Title</label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           name="title" 
                                           value="<?= htmlspecialchars($section['title']) ?>" 
                                           placeholder="Enter section title"
                                           required>
                                </div>
                                
                                <!-- Active Status -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="is_active" 
                                               id="active-<?= e($section['id']) ?>"
                                               <?= e($section['is_active'] ? 'checked' : '') ?>>
                                        <label class="form-check-label" for="active-<?= e($section['id']) ?>">
                                            Show on website
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Content Editor -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Content</label>
                                <textarea name="content" 
                                          class="form-control editor" 
                                          id="editor-<?= e($section['id']) ?>"
                                          rows="8"><?= htmlspecialchars($section['content']) ?></textarea>
                                <small class="text-muted">Use the toolbar above to format your text with bold, italic, links, and more.</small>
                            </div>
                            
                            <div class="row">
                                <!-- Image Upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Section Image</label>
                                    
                                    <!-- Current Image Preview -->
                                    <?php if (!empty($section['image'])): ?>
                                        <div class="mb-3 p-3 border rounded bg-light">
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/uploads/content/<?= htmlspecialchars($section['image']) ?>" 
                                                     alt="Current image" 
                                                     class="img-thumbnail me-3" 
                                                     style="max-width: 120px; max-height: 90px; object-fit: cover;"
                                                     id="current-image-<?= e($section['id']) ?>">
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 fw-bold">Current Image</p>
                                                    <small class="text-muted mb-2 d-block">Click "Upload Image" to replace</small>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            onclick="removeImage(<?= e($section['id']) ?>)">
                                                        <i class="fas fa-trash me-1"></i>Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Image Preview Area -->
                                    <div id="image-preview-<?= e($section['id']) ?>" class="mb-2" style="display: none;">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="mb-2 fw-bold text-success">Image Preview:</p>
                                            <img id="preview-img-<?= e($section['id']) ?>" 
                                                 src="" 
                                                 alt="Preview" 
                                                 class="img-fluid rounded" 
                                                 style="max-width: 200px; max-height: 150px; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" 
                                                    onclick="clearPreview(<?= e($section['id']) ?>)">
                                                <i class="fas fa-times me-1"></i>Clear Preview
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Upload Button -->
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-outline-primary" onclick="triggerFileInput(<?= e($section['id']) ?>)">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>Upload Image
                                        </button>
                                        <input type="file" 
                                               class="d-none" 
                                               id="image-input-<?= e($section['id']) ?>"
                                               name="image" 
                                               accept="image/jpeg,image/png,image/webp,image/gif"
                                               onchange="previewImage(this, <?= e($section['id']) ?>)">
                                    </div>
                                    
                                    <small class="text-muted d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Supported formats: JPG, PNG, WebP, GIF (Max: 5MB)
                                    </small>
                                    
                                    <?php if (empty($section['image'])): ?>
                                        <small class="text-info d-block">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Upload an image to make your section more engaging
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Button Settings -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Call-to-Action Button (Optional)</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" 
                                                   class="form-control mb-2" 
                                                   name="button_text" 
                                                   value="<?= htmlspecialchars($section['button_text']) ?>"
                                                   placeholder="Button text">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" 
                                                   class="form-control mb-2" 
                                                   name="button_link" 
                                                   value="<?= htmlspecialchars($section['button_link']) ?>"
                                                   placeholder="Button link">
                                        </div>
                                    </div>
                                    <small class="text-muted">Add a button to encourage user action</small>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <small class="text-muted">
                                    Last updated: <?= e(date('M j, Y g:i A', strtotime($section['updated_at']))) ?>
                                </small>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="previewSection('<?= e($section['section_name']) ?>')">
                                        <i class="fas fa-eye me-1"></i>Preview
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Quick Actions -->
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-tools me-2"></i>Quick Actions
                    </h5>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="homepage_editor.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-cogs me-2"></i>Advanced Editor
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../index.php" target="_blank" class="btn btn-outline-success w-100">
                                <i class="fas fa-external-link-alt me-2"></i>View Website
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button onclick="saveAllSections()" class="btn btn-outline-warning w-100">
                                <i class="fas fa-save me-2"></i>Save All
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="setup_homepage_cms.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-plus me-2"></i>Add Section
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
    </div>
</div>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- TinyMCE WYSIWYG Editor -->
<script src="https://cdn.tiny.cloud/1/xa7j2idk76099ylk6p43yo2ds5ssbzneba9liesymd94ygcq/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
// Initialize TinyMCE for all editors
tinymce.init({
    selector: '.editor',
    height: 400,
    skin: 'oxide', // Modern theme
    content_css: 'default', // Compatible with light/dark themes
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
    ],
    toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | ' +
             'alignleft aligncenter alignright alignjustify | ' +
             'bullist numlist outdent indent | removeformat | ' +
             'forecolor backcolor | link image media | emoticons charmap | ' +
             'fullscreen preview code | help',
    toolbar_mode: 'sliding',
    menubar: 'edit view insert format tools table help',
    statusbar: true,
    branding: false,
    promotion: false,
    image_advtab: true,
    image_title: true,
    automatic_uploads: false,
    file_picker_types: 'image',
    paste_data_images: true,
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; line-height: 1.6; }',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    }
});

// Toggle section visibility
function toggleSection(index) {
    const section = document.getElementById('section-' + index);
    const icon = document.getElementById('toggle-icon-' + index);
    
    if (section.style.display === 'none') {
        section.style.display = 'block';
        icon.className = 'fas fa-chevron-up';
    } else {
        section.style.display = 'none';
        icon.className = 'fas fa-chevron-down';
    }
}

// Preview section
function previewSection(sectionName) {
    const url = `preview_homepage.php?section=${encodeURIComponent(sectionName)}`;
    window.open(url, '_blank', 'width=800,height=600,scrollbars=yes');
}

// Trigger file input when upload button is clicked
function triggerFileInput(sectionId) {
    document.getElementById('image-input-' + sectionId).click();
}

// Preview image before upload
function previewImage(input, sectionId) {
    const previewDiv = document.getElementById('image-preview-' + sectionId);
    const previewImg = document.getElementById('preview-img-' + sectionId);
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            clearFileInput(sectionId);
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, WebP, or GIF)');
            clearFileInput(sectionId);
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewDiv.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Clear preview
function clearPreview(sectionId) {
    const previewDiv = document.getElementById('image-preview-' + sectionId);
    const input = document.getElementById('image-input-' + sectionId);
    
    previewDiv.style.display = 'none';
    clearFileInput(sectionId);
}

// Clear file input
function clearFileInput(sectionId) {
    const input = document.getElementById('image-input-' + sectionId);
    input.value = '';
}

// Remove current image
function removeImage(sectionId) {
    if (confirm('Are you sure you want to remove this image?')) {
        // Create a hidden input to indicate image removal
        const form = document.querySelector(`form[action="save_content.php"] input[name="id"][value="${sectionId}"]`).closest('form');
        
        // Add a hidden field to indicate image removal
        const removeInput = document.createElement('input');
        removeInput.type = 'hidden';
        removeInput.name = 'remove_image';
        removeInput.value = '1';
        form.appendChild(removeInput);
        
        // Hide current image display
        const currentImageDiv = document.getElementById('current-image-' + sectionId).closest('.bg-light');
        if (currentImageDiv) {
            currentImageDiv.style.display = 'none';
        }
        
        // Clear any preview
        clearPreview(sectionId);
        
        alert('Image will be removed when you save the changes.');
    }
}

// Save all sections (placeholder)
function saveAllSections() {
    alert('This feature will save all sections at once. Currently, please save each section individually.');
}

// Auto-save functionality (optional)
let autoSaveTimer;
function enableAutoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        console.log('Auto-save triggered');
        // Implement auto-save logic here
    }, 30000); // Auto-save every 30 seconds
}

// Initialize auto-save on content change
document.addEventListener('DOMContentLoaded', function() {
    const editors = document.querySelectorAll('.editor');
    editors.forEach(editor => {
        editor.addEventListener('input', enableAutoSave);
    });
});
</script>

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

</body>
</html>

<?php
// Helper function for section icons
function getSectionIcon($section) {
    $icons = [
        'hero' => 'home',
        'about' => 'info-circle',
        'features' => 'star',
        'testimonials' => 'quote-left',
        'cta' => 'bullhorn',
        'services' => 'cogs',
        'contact' => 'envelope',
        'gallery' => 'images',
        'team' => 'users'
    ];
    
    return $icons[$section] ?? 'file-alt';
}
?>
