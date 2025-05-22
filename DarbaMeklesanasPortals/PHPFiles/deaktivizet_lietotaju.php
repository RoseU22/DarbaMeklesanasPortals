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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lietotajsID'])) {
    $lietotajsID = $_POST['lietotajsID'];

    $stmt = $savienojums->prepare("SELECT lietotajvards FROM DMPortals WHERE lietotajsID = ?");
    $stmt->bind_param("i", $lietotajsID);
    $stmt->execute();
    $stmt->bind_result($deactivatedUsername);
    $stmt->fetch();
    $stmt->close();

    $sql = "UPDATE DMPortals SET statuss = 'deaktivizets' WHERE lietotajsID = ?";
    $stmt = $savienojums->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $lietotajsID);
        if ($stmt->execute()) {
            $apraksts = "Deaktivizēja lietotāju '$deactivatedUsername'.";
            logAdminAction($savienojums, $admin_id, $admin_lietotajvards, $apraksts);
            header("Location: ../Admin/admin_panelis.php");
            exit();
        } else {
            echo "Neizdevās deaktivizēt lietotāju.";
        }
        $stmt->close();
    } else {
        echo "Kļūda vaicājumā.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uznemumsID'])) {
    $uznemumsID = $_POST['uznemumsID'];

    $stmt = $savienojums->prepare("SELECT uznemuma_nosaukums FROM DMPortals_Uznemums WHERE uznemumsID = ?");
    $stmt->bind_param("i", $uznemumsID);
    $stmt->execute();
    $stmt->bind_result($companyName);
    $stmt->fetch();
    $stmt->close();

    $sql = "UPDATE DMPortals_Uznemums SET statuss = 'deaktivizets' WHERE uznemumsID = ?";
    $stmt = $savienojums->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $uznemumsID);
        if ($stmt->execute()) {
            $apraksts = "Deaktivizēja uzņēmumu '$companyName'.";
            logAdminAction($savienojums, $admin_id, $admin_lietotajvards, $apraksts);
            header("Location: ../Admin/admin_panelis.php");
            exit();
        } else {
            echo "Neizdevās deaktivizēt uzņēmumu.";
        }
        $stmt->close();
    } else {
        echo "Kļūda vaicājumā.";
    }
}

$savienojums->close();
?>
