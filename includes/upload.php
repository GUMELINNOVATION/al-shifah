<?php
// includes/upload.php
// AJAX file upload handler — returns JSON {success, url, error}

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No file received or upload error.']);
    exit;
}

$file     = $_FILES['file'];
$maxSize  = 1024 * 1024 * 1024; // 1GB (virtually no limit for standard use)
$type     = $file['type'];
$tmpPath  = $file['tmp_name'];
$origName = basename($file['name']);
$ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

// Determine folder by type
$allowedImages = ['jpg','jpeg','png','gif','webp','svg'];
$allowedVideos = ['mp4','webm','mov','avi','mkv'];
$allowedDocs   = ['pdf','doc','docx'];

if (in_array($ext, $allowedImages)) {
    $folder = 'images';
} elseif (in_array($ext, $allowedVideos)) {
    $folder = 'videos';
} elseif (in_array($ext, $allowedDocs)) {
    $folder = 'documents';
} else {
    $folder = 'general';
}

$uploadDir = __DIR__ . '/../uploads/' . $folder . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$safeName  = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
$filename  = time() . '_' . $safeName . '.' . $ext;
$destPath  = $uploadDir . $filename;

if (move_uploaded_file($tmpPath, $destPath)) {
    $url = 'uploads/' . $folder . '/' . $filename;
    echo json_encode(['success' => true, 'url' => $url, 'name' => $origName]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save the file. Check server permissions.']);
}
