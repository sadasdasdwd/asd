<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = "./"; // Save in the current directory
    $fileName = $_POST['fileName'];
    $chunkIndex = $_POST['chunkIndex'];
    $totalChunks = $_POST['totalChunks'];

    // Create a temporary file to store the chunk
    $tempFilePath = $targetDir . $fileName . '.part' . $chunkIndex;

    // Move the uploaded chunk to the temporary file
    if (move_uploaded_file($_FILES['fileChunk']['tmp_name'], $tempFilePath)) {
        // Check if all chunks are uploaded
        if ($chunkIndex == $totalChunks - 1) {
            // All chunks uploaded, combine them
            $finalFilePath = $targetDir . $fileName;
            $out = fopen($finalFilePath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $tempFilePath = $targetDir . $fileName . '.part' . $i;
                $in = fopen($tempFilePath, 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
                unlink($tempFilePath); // Remove the temporary chunk
            }

            fclose($out);
            echo "Upload complete!";
        } else {
            echo "Chunk uploaded successfully.";
        }
    } else {
        echo "Failed to upload chunk.";
    }
    exit; // End the script after handling the upload
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <input type="file" id="fileInput" />
    <button id="uploadButton">Upload</button>
    <progress id="progressBar" value="0" max="100" style="display:none;"></progress>
    <div id="status"></div>

    <script>
        document.getElementById('uploadButton').addEventListener('click', function() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            const chunkSize = 1024 * 1024; // 1MB
            const totalChunks = Math.ceil(file.size / chunkSize);
            let currentChunk = 0;

            const uploadChunk = (start) => {
                const end = Math.min(start + chunkSize, file.size);
                const chunk = file.slice(start, end);
                const formData = new FormData();
                formData.append('fileChunk', chunk);
                formData.append('chunkIndex', currentChunk);
                formData.append('totalChunks', totalChunks);
                formData.append('fileName', file.name);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true); // POST to the same file

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        currentChunk++;
                        const progress = Math.round((currentChunk / totalChunks) * 100);
                        document.getElementById('progressBar').value = progress;
                        document.getElementById('status').innerText = `Uploaded ${progress}%`;

                        if (currentChunk < totalChunks) {
                            uploadChunk(start + chunkSize);
                        } else {
                            document.getElementById('status').innerText = 'Upload complete!';
                        }
                    } else {
                        document.getElementById('status').innerText = 'Upload failed!';
                    }
                };

                xhr.send(formData);
            };

            uploadChunk(0);
            document.getElementById('progressBar').style.display = 'block';
        });
    </script>
</body>
</html>
