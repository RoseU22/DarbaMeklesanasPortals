<?php
require 'PHPFiles/con_db.php';
session_start();

// Dabūd iesūtītā CV lietotāja profila bildi
if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];

    if ($type === 'klients') {
        $stmt = $savienojums->prepare("SELECT profila_bilde FROM DMPortals WHERE lietotajsID = ?");
        $stmt->bind_param("i", $id);
    } elseif ($type === 'uznemums') {
        $stmt = $savienojums->prepare("SELECT profila_bilde FROM DMPortals_Uznemums WHERE uznemumsID = ?");
        $stmt->bind_param("i", $id);
    }

    if (isset($stmt)) {
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($image_data);
        $stmt->fetch();

        if ($image_data) {
            header("Content-Type: image/jpeg");
            echo $image_data;
        } else {
            header("Content-Type: image/png");
            readfile("Bildes/DefaultIcon.png");
        }
        exit();
    }
}


// Parāda lietotāja profila bildi
$lietotajvards = $_SESSION['username'];
$user_type = $_SESSION['userType'];

if ($user_type === 'klients') {
    $stmt = $savienojums->prepare("SELECT profila_bilde FROM DMPortals WHERE lietotajvards = ?");
    $stmt->bind_param("s", $lietotajvards);
} else {
    $stmt = $savienojums->prepare("SELECT profila_bilde FROM DMPortals_Uznemums WHERE uznemuma_nosaukums = ?");
    $stmt->bind_param("s", $lietotajvards);
}

$stmt->execute();
$stmt->store_result();
$stmt->bind_result($image_data);
$stmt->fetch();

if ($image_data) {
    header("Content-Type: image/jpeg");
    echo $image_data;
} else {
    header("Location: Bildes/DefaultIcon.png");
}
