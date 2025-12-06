<?php
require_once __DIR__ . '/../app/Core/Helpers.php';
// Preview individual section content
$section = $_GET['section'] ?? 'hero';

// Database connection
$host = 'localhost';
$dbname = 'meatme_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch section content
    $stmt = $pdo->prepare("SELECT * FROM homepage_content WHERE section_name = ? LIMIT 1");
    $stmt->execute([$section]);
    $sectionData = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $sectionData = null;
}

if (!$sectionData) {
    $sectionData = [
        'title' => 'Section Not Found',
        'content' => 'The requested section could not be loaded.',
        'image' => null,
        'button_text' => null,
        'button_link' => null
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: <?= htmlspecialchars($sectionData['title']) ?> - MeatMe</title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .preview-header {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .section-preview {
            min-height: 400px;
            padding: 3rem 0;
        }
        .hero-preview {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            color: white;
        }
        .about-preview {
            background: #f8f9fa;
        }
        .cta-preview {
            background: linear-gradient(135deg, #2196f3, #1976d2);
            color: white;
        }
    </style>
</head>
<body>
    
    <!-- Preview Header -->
    <div class="preview-header text-center">
        <div class="container">
            <h1><i class="fas fa-eye me-2"></i>Section Preview</h1>
            <p class="mb-0">Section: <strong><?= e(ucwords(str_replace('_', ' ', $section))) ?></strong></p>
        </div>
    </div>

    <!-- Preview Content -->
    <div class="section-preview <?= e($section) ?>-preview">
        <div class="container">
            
            <?php if ($section === 'hero'): ?>
                <!-- Hero Section Preview -->
                <div class="text-center">
                    <h1 class="display-4 fw-bold mb-4"><?= htmlspecialchars($sectionData['title']) ?></h1>
                    <div class="lead mb-4"><?= e($sectionData['content']) ?></div>
                    
                    <?php if ($sectionData['image']): ?>
                        <div class="mb-4">
                            <img src="../assets/uploads/homepage/<?= htmlspecialchars($sectionData['image']) ?>" 
                                 alt="Section image" 
                                 class="img-fluid rounded shadow" 
                                 style="max-height: 400px;">
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($sectionData['button_text']): ?>
                        <a href="<?= htmlspecialchars($sectionData['button_link'] ?: '#') ?>" class="btn btn-light btn-lg">
                            <?= htmlspecialchars($sectionData['button_text']) ?>
                        </a>
                    <?php endif; ?>
                </div>
                
            <?php elseif ($section === 'cta'): ?>
                <!-- CTA Section Preview -->
                <div class="text-center">
                    <h2 class="fw-bold mb-4"><?= htmlspecialchars($sectionData['title']) ?></h2>
                    <div class="lead mb-4"><?= e($sectionData['content']) ?></div>
                    
                    <?php if ($sectionData['image']): ?>
                        <div class="mb-4">
                            <img src="../assets/uploads/homepage/<?= htmlspecialchars($sectionData['image']) ?>" 
                                 alt="Section image" 
                                 class="img-fluid rounded shadow" 
                                 style="max-height: 300px;">
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($sectionData['button_text']): ?>
                        <a href="<?= htmlspecialchars($sectionData['button_link'] ?: '#') ?>" class="btn btn-light btn-lg">
                            <?= htmlspecialchars($sectionData['button_text']) ?>
                        </a>
                    <?php endif; ?>
                </div>
                
            <?php else: ?>
                <!-- Generic Section Preview -->
                <div class="row align-items-center">
                    <div class="col-lg-<?= e($sectionData['image'] ? '6' : '12') ?>">
                        <h2 class="fw-bold mb-4"><?= htmlspecialchars($sectionData['title']) ?></h2>
                        <div class="mb-4"><?= e($sectionData['content']) ?></div>
                        
                        <?php if ($sectionData['button_text']): ?>
                            <a href="<?= htmlspecialchars($sectionData['button_link'] ?: '#') ?>" class="btn btn-primary">
                                <?= htmlspecialchars($sectionData['button_text']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($sectionData['image']): ?>
                        <div class="col-lg-6 text-center">
                            <img src="../assets/uploads/homepage/<?= htmlspecialchars($sectionData['image']) ?>" 
                                 alt="Section image" 
                                 class="img-fluid rounded shadow">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
    
    <!-- Preview Info -->
    <div class="container mb-4">
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle me-2"></i>Preview Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <strong>Section:</strong> <?= htmlspecialchars($section) ?><br>
                    <strong>Title:</strong> <?= htmlspecialchars($sectionData['title']) ?><br>
                    <strong>Has Image:</strong> <?= e($sectionData['image'] ? 'Yes' : 'No') ?>
                </div>
                <div class="col-md-6">
                    <strong>Has Button:</strong> <?= e($sectionData['button_text'] ? 'Yes' : 'No') ?><br>
                    <strong>Content Length:</strong> <?= e(strlen(strip_tags($sectionData['content']))) ?> characters<br>
                    <strong>Last Updated:</strong> <?= e(isset($sectionData['updated_at']) ? date('M j, Y g:i A', strtotime($sectionData['updated_at'])) : 'Unknown') ?>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="text-center">
            <button onclick="window.close()" class="btn btn-secondary me-2">
                <i class="fas fa-times me-2"></i>Close Preview
            </button>
            <a href="../index.php" target="_blank" class="btn btn-success">
                <i class="fas fa-external-link-alt me-2"></i>View Full Website
            </a>
        </div>
    </div>

    <!-- MDBootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
</body>
</html>
