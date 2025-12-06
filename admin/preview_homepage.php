<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
}

// Preview homepage content (for testing before saving)
$section_name = $_POST['section_name'] ?? 'hero';
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$button_text = $_POST['button_text'] ?? '';
$button_link = $_POST['button_link'] ?? '';
$css_classes = $_POST['css_classes'] ?? '';

// Sanitize fields to prevent XSS while preserving admin preview functionality
$button_link = filter_var($button_link, FILTER_VALIDATE_URL) ? $button_link : '#';
$css_classes = preg_replace('/[^a-zA-Z0-9\\s\\-_]/', '', $css_classes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preview: <?= e($title) ?> - MeatMe</title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .preview-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .section-preview {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            background: #f8f9fa;
        }
        .hero-section {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            color: white;
            padding: 4rem 0;
        }
        .about-section {
            padding: 3rem 0;
            background: #f8f9fa;
        }
        .features-section {
            padding: 3rem 0;
        }
        .cta-section {
            background: linear-gradient(135deg, #2196f3, #1976d2);
            color: white;
            padding: 3rem 0;
        }
    </style>
</head>
<body>
    
    <!-- Preview Header -->
    <div class="preview-header text-center">
        <div class="container">
            <h1><i class="fas fa-eye me-2"></i>Content Preview</h1>
            <p class="mb-0">Section: <strong><?= e(ucfirst($section_name)) ?></strong></p>
        </div>
    </div>

    <!-- Preview Content -->
    <div class="container">
        <div class="section-preview <?= e($css_classes) ?>">
            
            <?php if ($section_name === 'hero'): ?>
                <!-- Hero Section Preview -->
                <div class="hero-section text-center">
                    <div class="container">
                        <h1 class="display-4 fw-bold mb-4"><?= e($title) ?></h1>
                        <div class="lead mb-4"><?= nl2br(e($content)) ?></div>
                        <?php if ($button_text): ?>
                            <a href="<?= e($button_link ?: '#') ?>" class="btn btn-light btn-lg">
                                <?= e($button_text) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
            <?php elseif ($section_name === 'about'): ?>
                <!-- About Section Preview -->
                <div class="about-section">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h2 class="fw-bold mb-4"><?= e($title) ?></h2>
                                <div class="mb-4"><?= nl2br(e($content)) ?></div>
                                <?php if ($button_text): ?>
                                    <a href="<?= e($button_link ?: '#') ?>" class="btn btn-success">
                                        <?= e($button_text) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-6 text-center">
                                <i class="fas fa-drumstick-bite fa-5x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($section_name === 'cta'): ?>
                <!-- CTA Section Preview -->
                <div class="cta-section text-center">
                    <div class="container">
                        <h2 class="fw-bold mb-4"><?= e($title) ?></h2>
                        <div class="lead mb-4"><?= nl2br(e($content)) ?></div>
                        <?php if ($button_text): ?>
                            <a href="<?= e($button_link ?: '#') ?>" class="btn btn-light btn-lg">
                                <?= e($button_text) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Generic Section Preview -->
                <div class="features-section">
                    <div class="container">
                        <div class="text-center">
                            <h2 class="fw-bold mb-4"><?= e($title) ?></h2>
                            <div class="mb-4"><?= nl2br(e($content)) ?></div>
                            <?php if ($button_text): ?>
                                <a href="<?= e($button_link ?: '#') ?>" class="btn btn-primary">
                                    <?= e($button_text) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Preview Info -->
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle me-2"></i>Preview Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <strong>Section:</strong> <?= e($section_name) ?><br>
                    <strong>Title:</strong> <?= e($title) ?><br>
                    <strong>Button:</strong> <?= e($button_text ?: 'None') ?>
                </div>
                <div class="col-md-6">
                    <strong>CSS Classes:</strong> <?= e($css_classes ?: 'None') ?><br>
                    <strong>Content Length:</strong> <?= e(strlen(strip_tags($content))) ?> characters<br>
                    <strong>Button Link:</strong> <?= e($button_link ?: 'None') ?>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="text-center mb-4">
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Close Preview
            </button>
        </div>
    </div>

    <!-- MDBootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
</body>
</html>
