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
    header('Location: content_editor.php?error=' . urlencode('Invalid request method'));
    exit;
}

try {
    // Get form data
    $id = intval($_POST['id'] ?? 0);
    $section_name = trim($_POST['section_name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $button_text = trim($_POST['button_text'] ?? '') ?: null;
    $button_link = trim($_POST['button_link'] ?? '') ?: null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate required fields
    if ($id <= 0) {
        throw new Exception('Invalid section ID');
    }
    
    if (empty($title)) {
        throw new Exception('Section title is required');
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
            $stmt = $mysqli->prepare("SELECT image FROM homepage_content WHERE id = ?");
            $stmt->bind_param("i", $id);
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
        global $mysqli;
        $stmt = $mysqli->prepare("SELECT image FROM homepage_content WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $oldImage = $row['image'] ?? null;
        
        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }
        
        $image_filename = null;
        $update_image = true;
    }
    
    // Update database
    global $mysqli;
    if ($update_image) {
        $stmt = $mysqli->prepare("
            UPDATE homepage_content SET 
                title = ?, content = ?, image = ?, button_text = ?, 
                button_link = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->bind_param("sssssii", $title, $content, $image_filename, $button_text,
            $button_link, $is_active, $id);
        $stmt->execute();
        $affectedRows = $mysqli->affected_rows;
    } else {
        $stmt = $mysqli->prepare("
            UPDATE homepage_content SET 
                title = ?, content = ?, button_text = ?, 
                button_link = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->bind_param("ssssii", $title, $content, $button_text, $button_link, $is_active, $id);
        $stmt->execute();
        $affectedRows = $mysqli->affected_rows;
    }
    
    // Check if update was successful
    if ($affectedRows === 0) {
        // Check if the record exists
        $checkStmt = $mysqli->prepare("SELECT id FROM homepage_content WHERE id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if (!$result->fetch_assoc()) {
            throw new Exception('Content section not found');
        }
        
        // If record exists but no rows were affected, it means no changes were made
        $successMessage = 'No changes were made to the content';
    } else {
        $successMessage = 'Content updated successfully';
    }
    
    // Log the action
    error_log("Content section ID {$id} ({$section_name}) updated by admin user ID: " . ($_SESSION['admin_id'] ?? 'unknown'));
    
    // Redirect with success message
    header('Location: content_editor.php?success=1&message=' . urlencode($successMessage));
    exit;
    
} catch (Exception $e) {
    // Redirect with error message
    error_log("Error in save_content.php: " . $e->getMessage());
    header('Location: content_editor.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>
