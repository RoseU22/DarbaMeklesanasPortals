<?php
session_start();
require 'con_db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uznemumsID = intval($_POST["uznemumsID"] ?? 0);

    if ($uznemumsID > 0) {
        $stmt = $savienojums->prepare("UPDATE DMPortals_Uznemums SET apstiprinats = 'apstiprinats' WHERE uznemumsID = ?");
        $stmt->bind_param("i", $uznemumsID);

        if ($stmt->execute()) {
            header("Location: ../Admin/admin_panelis.php");
            exit;
        } else {
            echo "Neizdevās apstiprināt lietotāju.";
        }
        $stmt->close();
    } else {
        echo "Nepareizs uzņēmuma ID.";
    }
}
?>
