<?php
session_start();
require 'con_db.php';

function logAdminAction($savienojums, $admin_id, $admin_lietotajvards, $apraksts) {
    $stmt = $savienojums->prepare("INSERT INTO DMPortals_AdminLog (admin_id, admin_lietotajvards, apraksts, laiks) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $admin_id, $admin_lietotajvards, $apraksts);
    $stmt->execute();
    $stmt->close();
}

if (!isset($_SESSION['username'])) {
    echo "Admin nav pieslēdzies.";
    exit();
}

$admin_lietotajvards = $_SESSION['username'];
$stmt = $savienojums->prepare("SELECT lietotajsID FROM DMPortals WHERE lietotajvards = ?");
$stmt->bind_param("s", $admin_lietotajvards);
$stmt->execute();
$stmt->bind_result($admin_id);
if (!$stmt->fetch()) {
    echo "Neizdevās atrast administratora informāciju.";
    exit();
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uznemumsID = intval($_POST["uznemumsID"] ?? 0);

    if ($uznemumsID > 0) {
        $stmt = $savienojums->prepare("UPDATE DMPortals_Uznemums SET apstiprinats = 'apstiprinats' WHERE uznemumsID = ?");
        $stmt->bind_param("i", $uznemumsID);

        if ($stmt->execute()) {

            $stmt = $savienojums->prepare("SELECT uznemuma_nosaukums FROM DMPortals_Uznemums WHERE uznemumsID = ?");
            $stmt->bind_param("i", $uznemumsID);
            $stmt->execute();
            $stmt->bind_result($companyName);
            $stmt->fetch();
            $stmt->close();

            $apraksts = "Apstiprināja lietotāju '$companyName'.";
            logAdminAction($savienojums, $admin_id, $admin_lietotajvards, $apraksts);
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
