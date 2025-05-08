<?php
require 'PHPFiles/con_db.php';
session_start();

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
