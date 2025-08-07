<?php
function scan_port($ip, $port, $timeout = 1) {
    $connection = @fsockopen($ip, $port, $errno, $errstr, $timeout);
    if (is_resource($connection)) {
        fclose($connection);
        return true;
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Port Scanner</title>
</head>
<body>
    <h2>PHP Port Scanner</h2>
    <form method="POST">
        <label for="ip">IP Address:</label><br>
        <input type="text" id="ip" name="ip" required><br><br>

        <label for="ports">Ports (comma-separated):</label><br>
        <input type="text" id="ports" name="ports" placeholder="e.g., 21,22,80,443" required><br><br>

        <input type="submit" value="Scan">
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_POST['ip'];
    $ports_input = $_POST['ports'];
    $ports = array_map('trim', explode(',', $ports_input));
    echo "<h3>Scan Results for $ip:</h3><ul>";

    foreach ($ports as $port) {
        if (is_numeric($port)) {
            $status = scan_port($ip, (int)$port) ? '✅ Open' : '❌ Closed';
            echo "<li>Port $port: $status</li>";
        } else {
            echo "<li>Port '$port': ⚠️ Invalid</li>";
        }
    }

    echo "</ul>";
}
?>

</body>
</html>
