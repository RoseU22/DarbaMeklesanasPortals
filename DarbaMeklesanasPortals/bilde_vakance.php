<?php
require 'PHPFiles/con_db.php';
session_start();

if (isset($_GET['id'])) {
    $vakance_id = intval($_GET['id']);

    $stmt = $savienojums->prepare("SELECT bilde FROM DMPortals_Vakances WHERE vakancesID = ?");
    $stmt->bind_param("i", $vakance_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($image_data);
    $stmt->fetch();

    if ($image_data) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $image_data);
        finfo_close($finfo);

        $mime = $mime ?: 'image/jpeg';

        header("Content-Type: $mime");
        echo $image_data;
    } else {
        header("Content-Type: image/png");
        readfile("Bildes/Vakance.png");
    }
    exit();
}

http_response_code(404);
echo "Image not found.";
exit();
?>
