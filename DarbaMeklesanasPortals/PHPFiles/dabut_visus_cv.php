<?php
session_start();
require 'con_db.php';

if (!isset($_SESSION["username"])) {
    echo json_encode(['success' => false, 'message' => 'Lietotājs nav pieslēdzies.']);
    exit;
}

$username = $_SESSION["username"];
$query = "SELECT id, vards FROM DMPortals_CV WHERE lietotajvards = ?";
$stmt = $savienojums->prepare($query);
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $cvs = [];

    while ($row = $result->fetch_assoc()) {
        $cvs[] = $row;
    }

    echo json_encode($cvs);
} else {
    echo json_encode(['success' => false, 'message' => 'Kļūda iegūstot CV sarakstu.']);
}

$stmt->close();
$savienojums->close();
?>
