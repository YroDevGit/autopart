<?php
// cimagepicker.php - Place this in your project root

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    $publicFolder = "public";
    $path =  "views/core/partials/storage/$publicFolder/";
    $fullPath = $path;
    
    if (!is_dir($fullPath)) {
        echo json_encode(['images' => []]);
        exit;
    }
    
    $images = [];
    $imageTypes = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg', 'ico'];
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $imageTypes)) {
                $relativePath = str_replace(dirname(__DIR__) . '/', '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);
                
                $images[] = [
                    'name' => $file->getFilename(),
                    'path' => $relativePath,
                    'url' => "/ctrstorage/$publicFolder/". $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                    'extension' => $extension,
                    'type' => 'image'
                ];
            }
        }
    }
    
    usort($images, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    
    echo json_encode(['images' => array_values($images)]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'upload') {
    $path = isset($_POST['path']) ? $_POST['path'] : 'views/core/partials/storage/public';
    $fullPath = dirname(__DIR__) . '/' . $path;
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No image uploaded or upload error']);
        exit;
    }
    
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
    }
    
    $file = $_FILES['image'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $imageTypes = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg', 'ico'];
    
    if (!in_array($extension, $imageTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type']);
        exit;
    }
    
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filePath = $fullPath . DIRECTORY_SEPARATOR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $relativePath = str_replace(dirname(__DIR__) . '/', '', $filePath);
        $relativePath = str_replace('\\', '/', $relativePath);
        
        $imageData = [
            'name' => $filename,
            'path' => $relativePath,
            'url' => '/' . $relativePath,
            'size' => $file['size'],
            'modified' => time(),
            'extension' => $extension,
            'type' => 'image'
        ];
        
        echo json_encode([
            'success' => true,
            'image' => $imageData,
            'message' => 'Image uploaded successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to move uploaded image'
        ]);
    }
    exit;
}

echo json_encode(['error' => 'Invalid request']);
?>