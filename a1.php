<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    echo move_uploaded_file($file['tmp_name'], __DIR__ . '/' . basename($file['name'])) 
        ? "Uploaded: " . htmlspecialchars($file['name']) 
        : "Error uploading file.";
}
?>
<!DOCTYPE html>
<html>
<head><title>File Upload</title></head>
<body>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>
</body>
</html>
