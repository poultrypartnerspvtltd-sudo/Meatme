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

$page_title = 'Homepage Content Editor';

// Get selected section or default to hero
$selected_section = $_GET['section'] ?? 'hero';

// Fetch all sections for navigation
global $mysqli;
$result = $mysqli->query("SELECT section_name, title FROM homepage_content ORDER BY sort_order ASC");
$all_sections = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $all_sections[] = $row;
    }
}

// Fetch current section content
$current_content = null;
$stmt = $mysqli->prepare("SELECT * FROM homepage_content WHERE section_name = ?");
$stmt->bind_param("s", $selected_section);
$stmt->execute();
$result = $stmt->get_result();
$current_content = $result->fetch_assoc();

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Homepage Content Editor</h2>
            <p class="text-muted mb-0">Manage your homepage content with WYSIWYG editor</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-eye me-2"></i>Preview Homepage
                </a>
                <a href="setup_homepage_cms.php" class="btn btn-success">
                    <i class="fas fa-database me-2"></i>Setup CMS
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>Homepage content updated successfully!
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Section Navigation -->
    <div class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Homepage Sections
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($all_sections as $section): ?>
                        <a href="?section=<?= e(urlencode($section['section_name'])) ?>" 
                           class="list-group-item list-group-item-action <?= e($section['section_name'] === $selected_section ? 'active' : '') ?>">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-<?= e(getSectionIcon($section['section_name'])) ?> me-2"></i>
                                <div>
                                    <div class="fw-bold"><?= e(ucfirst($section['section_name'])) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars(substr($section['title'] ?? '', 0, 30)) ?>...</small>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-outline-success btn-sm w-100" onclick="addNewSection()">
                    <i class="fas fa-plus me-2"></i>Add New Section
                </button>
            </div>
        </div>
    </div>

    <!-- Content Editor -->
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit <?= e(ucfirst($selected_section)) ?> Section
                    </h5>
                    <?php if ($current_content): ?>
                        <small class="text-muted">
                            Last updated: <?= e(date('M j, Y g:i A', strtotime($current_content['updated_at']))) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="save_homepage.php" enctype="multipart/form-data" id="homepageForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="section_name" value="<?= htmlspecialchars($selected_section) ?>">
                    
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Section Title</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="title" 
                                   value="<?= htmlspecialchars($current_content['title'] ?? '') ?>"
                                   placeholder="Enter section title">
                        </div>
                        
                        <!-- Sort Order -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Display Order</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="sort_order" 
                                   value="<?= e($current_content['sort_order'] ?? 1) ?>"
                                   min="1">
                        </div>
                    </div>

                    <!-- Content Editor -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Content</label>
                        <textarea name="content" 
                                  id="contentEditor" 
                                  class="form-control"
                                  rows="10"><?= htmlspecialchars($current_content['content'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <!-- Image Upload -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Section Image</label>
                            
                            <!-- Current Image Preview -->
                            <?php if (!empty($current_content['image'])): ?>
                                <div class="mb-3 p-3 border rounded bg-light">
                                    <div class="d-flex align-items-center">
                                        <img src="../assets/uploads/content/<?= htmlspecialchars($current_content['image']) ?>" 
                                             alt="Current image" 
                                             class="img-thumbnail me-3" 
                                             style="max-width: 120px; max-height: 90px; object-fit: cover;"
                                             id="current-image-hero">
                                        <div class="flex-grow-1">
                                            <p class="mb-1 fw-bold">Current Image</p>
                                            <small class="text-muted mb-2 d-block">Click "Upload Image" to replace</small>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="removeImage('hero')">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Image Preview Area -->
                            <div id="image-preview-hero" class="mb-2" style="display: none;">
                                <div class="border rounded p-2 bg-light">
                                    <p class="mb-2 fw-bold text-success">Image Preview:</p>
                                    <img id="preview-img-hero" 
                                         src="" 
                                         alt="Preview" 
                                         class="img-fluid rounded" 
                                         style="max-width: 200px; max-height: 150px; object-fit: cover;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" 
                                            onclick="clearPreview('hero')">
                                        <i class="fas fa-times me-1"></i>Clear Preview
                                    </button>
                                </div>
                            </div>

                            <!-- Upload Button -->
                            <div class="mb-2">
                                <button type="button" class="btn btn-outline-primary" onclick="triggerFileInput('hero')">
                                    <i class="fas fa-cloud-upload-alt me-2"></i>Upload Image
                                </button>
                                <input type="file" 
                                       class="d-none" 
                                       id="image-input-hero"
                                       name="image" 
                                       accept="image/jpeg,image/png,image/webp,image/gif"
                                       onchange="previewImage(this, 'hero')">
                            </div>
                            
                            <small class="text-muted d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Supported formats: JPG, PNG, WebP, GIF (Max: 5MB)
                            </small>
                            
                            <?php if (empty($current_content['image'])): ?>
                                <small class="text-info d-block">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Upload an image to make your section more engaging
                                </small>
                            <?php endif; ?>
                        </div>

                        <!-- Active Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       id="is_active"
                                       <?= e(($current_content['is_active'] ?? 1) ? 'checked' : '') ?>>
                                <label class="form-check-label" for="is_active">
                                    Active (visible on homepage)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Button Settings (for CTA sections) -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Button Text (Optional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="button_text" 
                                   value="<?= htmlspecialchars($current_content['button_text'] ?? '') ?>"
                                   placeholder="e.g., Shop Now, Learn More">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Button Link (Optional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="button_link" 
                                   value="<?= htmlspecialchars($current_content['button_link'] ?? '') ?>"
                                   placeholder="e.g., /products, /about">
                        </div>
                    </div>

                    <!-- CSS Classes -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Custom CSS Classes (Advanced)</label>
                        <input type="text" 
                               class="form-control" 
                               name="css_classes" 
                               value="<?= htmlspecialchars($current_content['css_classes'] ?? '') ?>"
                               placeholder="e.g., text-center bg-primary text-white">
                        <small class="text-muted">Add Bootstrap classes for custom styling</small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <?php if ($current_content): ?>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteSection()">
                                    <i class="fas fa-trash me-2"></i>Delete Section
                                </button>
                            <?php endif; ?>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="previewContent()">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- TinyMCE WYSIWYG Editor -->
<script src="https://cdn.tiny.cloud/1/xa7j2idk76099ylk6p43yo2ds5ssbzneba9liesymd94ygcq/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
// Initialize TinyMCE
tinymce.init({
    selector: '#contentEditor',
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

// Delete section function
function deleteSection() {
    if (confirm('Are you sure you want to delete this section? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete_homepage_section.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'section_name';
        input.value = '<?= htmlspecialchars($selected_section) ?>';
        
        const csrfToken =
            document.querySelector('#homepageForm input[name="csrf_token"]')?.value ||
            document.querySelector('#csrf-token-form input[name="csrf_token"]')?.value ||
            '';
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'csrf_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);
        }

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Preview content function
function previewContent() {
    // Save current content to TinyMCE
    tinymce.triggerSave();
    
    // Open preview in new window
    const form = document.getElementById('homepageForm');
    const formData = new FormData(form);
    
    // Create preview form
    const previewForm = document.createElement('form');
    previewForm.method = 'POST';
    previewForm.action = 'preview_homepage.php';
    previewForm.target = '_blank';
    
    for (let [key, value] of formData.entries()) {
        if (key !== 'image') { // Skip file input for preview
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            previewForm.appendChild(input);
        }
    }
    
    document.body.appendChild(previewForm);
    previewForm.submit();
    document.body.removeChild(previewForm);
}

// Trigger file input when upload button is clicked
function triggerFileInput(section) {
    document.getElementById('image-input-' + section).click();
}

// Preview image before upload
function previewImage(input, section) {
    const previewDiv = document.getElementById('image-preview-' + section);
    const previewImg = document.getElementById('preview-img-' + section);
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            clearFileInput(section);
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, WebP, or GIF)');
            clearFileInput(section);
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
function clearPreview(section) {
    const previewDiv = document.getElementById('image-preview-' + section);
    const input = document.getElementById('image-input-' + section);
    
    previewDiv.style.display = 'none';
    clearFileInput(section);
}

// Clear file input
function clearFileInput(section) {
    const input = document.getElementById('image-input-' + section);
    input.value = '';
}

// Remove current image
function removeImage(section) {
    if (confirm('Are you sure you want to remove this image?')) {
        // Create a hidden input to indicate image removal
        const form = document.getElementById('homepageForm');
        
        // Add a hidden field to indicate image removal
        const removeInput = document.createElement('input');
        removeInput.type = 'hidden';
        removeInput.name = 'remove_image';
        removeInput.value = '1';
        form.appendChild(removeInput);
        
        // Hide current image display
        const currentImageDiv = document.getElementById('current-image-' + section).closest('.bg-light');
        if (currentImageDiv) {
            currentImageDiv.style.display = 'none';
        }
        
        // Clear any preview
        clearPreview(section);
        
        alert('Image will be removed when you save the changes.');
    }
}

// Form validation
document.getElementById('homepageForm').addEventListener('submit', function(e) {
    const title = document.querySelector('input[name="title"]').value.trim();
    
    if (!title) {
        e.preventDefault();
        alert('Please enter a section title');
        return false;
    }
    
    // Save TinyMCE content
    tinymce.triggerSave();
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
        'contact' => 'envelope'
    ];
    
    return $icons[$section] ?? 'file-alt';
}
?>
