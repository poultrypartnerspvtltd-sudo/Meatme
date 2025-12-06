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
    // Get form data
    $section_name = trim($_POST['section_name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $button_text = trim($_POST['button_text'] ?? '') ?: null;
    $button_link = trim($_POST['button_link'] ?? '') ?: null;
    $css_classes = trim($_POST['css_classes'] ?? '') ?: null;
    $sort_order = intval($_POST['sort_order'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate required fields
    if (empty($section_name)) {
        throw new Exception('Section name is required');
    }
    
    if (empty($title)) {
        throw new Exception('Section title is required');
    }
    
    // Validate section name format
    if (!preg_match('/^[a-z_]+$/', $section_name)) {
        throw new Exception('Section name must contain only lowercase letters and underscores');
    }
    
    // Handle image removal
    $remove_image = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';
    
    // Handle image upload
    $image_filename = null;
    $update_image = false;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/content/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid image format. Only JPG, PNG, WebP, and GIF are allowed.');
        }
        
        // Validate file size (max 5MB)
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            throw new Exception('Image file is too large. Maximum size is 5MB.');
        }
        
        // Generate unique filename
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $image_filename = $section_name . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $image_filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            $update_image = true;
            
            // Delete old image if it exists
            global $mysqli;
            $stmt = $mysqli->prepare("SELECT image FROM homepage_content WHERE section_name = ?");
            $stmt->bind_param("s", $section_name);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $oldImage = $row['image'] ?? null;
            
            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
        } else {
            throw new Exception('Failed to upload image');
        }
    } elseif ($remove_image) {
        // Handle image removal
        $uploadDir = '../assets/uploads/content/';
        $stmt = $pdo->prepare("SELECT image FROM homepage_content WHERE section_name = ?");
        $stmt->execute([$section_name]);
        $oldImage = $stmt->fetchColumn();
        
        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }
        
        $image_filename = null;
        $update_image = true;
    }
    
    // Check if section exists
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT id FROM homepage_content WHERE section_name = ?");
    $stmt->bind_param("s", $section_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingSection = $result->fetch_assoc();
    
    if ($existingSection) {
        // Update existing section
        if ($update_image) {
            $stmt = $mysqli->prepare("
                UPDATE homepage_content SET 
                    title = ?, content = ?, image = ?, button_text = ?, 
                    button_link = ?, css_classes = ?, sort_order = ?, 
                    is_active = ?, updated_at = CURRENT_TIMESTAMP
                WHERE section_name = ?
            ");
            $stmt->bind_param("ssssssiss", $title, $content, $image_filename, $button_text,
                $button_link, $css_classes, $sort_order, $is_active, $section_name);
            $stmt->execute();
        } else {
            $stmt = $mysqli->prepare("
                UPDATE homepage_content SET 
                    title = ?, content = ?, button_text = ?, 
                    button_link = ?, css_classes = ?, sort_order = ?, 
                    is_active = ?, updated_at = CURRENT_TIMESTAMP
                WHERE section_name = ?
            ");
            $stmt->bind_param("sssssiss", $title, $content, $button_text, $button_link, 
                $css_classes, $sort_order, $is_active, $section_name);
            $stmt->execute();
        }
    } else {
        // Insert new section
        $stmt = $mysqli->prepare("
            INSERT INTO homepage_content 
            (section_name, title, content, image, button_text, button_link, css_classes, sort_order, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssssii", $section_name, $title, $content, $image_filename, $button_text,
            $button_link, $css_classes, $sort_order, $is_active);
        $stmt->execute();
    }
    
    // Log the action
    error_log("Homepage section '{$section_name}' updated by admin user ID: " . ($_SESSION['admin_id'] ?? 'unknown'));
    
    // Redirect with success message
    header('Location: homepage_editor.php?section=' . urlencode($section_name) . '&success=1');
    exit;
    
} catch (Exception $e) {
    // Redirect with error message
    $section = $_POST['section_name'] ?? 'hero';
    header('Location: homepage_editor.php?section=' . urlencode($section) . '&error=' . urlencode($e->getMessage()));
    exit;
}
?>
