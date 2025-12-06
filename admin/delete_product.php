<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Invalid product ID';
    header('Location: products.php');
    exit;
}

$productId = intval($_GET['id']);

try {
    global $mysqli;
    
    // First, get product details to delete associated files
    $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        $_SESSION['error'] = 'Product not found';
        header('Location: products.php');
        exit;
    }
    
    // Get product images to delete files
    $productImages = [];
    $stmt = $mysqli->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $productImages[] = $row['image_path'];
        }
    } else {
        // If product_images table doesn't exist, just continue
        error_log("Product images fetch error: " . $mysqli->error);
    }
    
    // Start transaction
    $mysqli->autocommit(false);
    
    try {
        // Delete product images from database
        $stmt = $mysqli->prepare("DELETE FROM product_images WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $productId);
            $stmt->execute();
        } else {
            // If product_images table doesn't exist, just continue
            error_log("Product images delete error: " . $mysqli->error);
        }
        
        // Delete product from database
        $stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        
        // Check if product was actually deleted
        if ($mysqli->affected_rows === 0) {
            throw new Exception('Product not found or already deleted');
        }
        
        // Commit transaction
        $mysqli->commit();
        $mysqli->autocommit(true);
        
        // Delete image files from filesystem
        foreach ($productImages as $imagePath) {
            $fullPath = '../' . $imagePath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        
        $_SESSION['success'] = 'Product deleted successfully';
        
    } catch (Exception $e) {
        // Rollback transaction
        $mysqli->rollback();
        $mysqli->autocommit(true);
        throw $e;
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error deleting product: ' . $e->getMessage();
}

// Redirect back to products page
header('Location: products.php');
exit;
?>
