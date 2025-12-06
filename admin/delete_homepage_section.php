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

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: homepage_editor.php?error=' . urlencode('Invalid request method'));
    exit;
}

try {
    $section_name = trim($_POST['section_name'] ?? '');
    
    if (empty($section_name)) {
        throw new Exception('Section name is required');
    }
    
    // Get section details before deletion (for image cleanup)
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT image FROM homepage_content WHERE section_name = ?");
    $stmt->bind_param("s", $section_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $section = $result->fetch_assoc();
    
    if (!$section) {
        throw new Exception('Section not found');
    }
    
    // Delete from database
    $stmt = $mysqli->prepare("DELETE FROM homepage_content WHERE section_name = ?");
    $stmt->bind_param("s", $section_name);
    $stmt->execute();
    
    // Delete associated image file
    if ($section['image']) {
        $imagePath = '../assets/uploads/homepage/' . $section['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Log the action
    error_log("Homepage section '{$section_name}' deleted by admin user ID: " . ($_SESSION['admin_id'] ?? 'unknown'));
    
    // Redirect to first available section or hero
    $result = $mysqli->query("SELECT section_name FROM homepage_content ORDER BY sort_order ASC LIMIT 1");
    $firstSection = null;
    if ($result && $row = $result->fetch_assoc()) {
        $firstSection = $row['section_name'];
    }
    $redirectSection = $firstSection ?: 'hero';
    
    header('Location: homepage_editor.php?section=' . urlencode($redirectSection) . '&success=1');
    exit;
    
} catch (Exception $e) {
    header('Location: homepage_editor.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>
