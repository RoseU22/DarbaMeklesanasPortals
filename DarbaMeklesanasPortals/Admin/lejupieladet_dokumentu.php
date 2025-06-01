<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../PHPFiles/con_db.php';

if (isset($_GET['uznemumsID'])) {
    $uznemumsID = intval($_GET['uznemumsID']);

    $sql = "SELECT dokuments, dokumenta_nosaukums, mime_tips FROM DMPortals_Uznemums WHERE uznemumsID = ?";
    $stmt = $savienojums->prepare($sql);
    $stmt->bind_param("i", $uznemumsID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($dokuments, $nosaukums, $mime);
        $stmt->fetch();

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $nosaukums . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($dokuments));

        echo $dokuments;
        exit;
    } else {
        echo "Fails netika atrasts.";
    }

    $stmt->close();
} else {
    echo "Nepareizs pieprasījums.";
}
?>
