<?php
session_start();
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pazinojumi_id'], $_POST['zina'])) {
    $pazinojumi_id = intval($_POST['pazinojumi_id']);
    $zina = trim($_POST['zina']);

    if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'uznemums') {
        echo "invalid_user";
        exit();
    }

    $sql = "UPDATE DMPortals_Pazinojumi SET statuss = 'Akceptēts', zina = ? WHERE pazinojumi_id = ?";
    $stmt = $savienojums->prepare($sql);
    if (!$stmt) {
        echo "prepare_error";
        exit();
    }

    $stmt->bind_param("si", $zina, $pazinojumi_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $savienojums->close();
} else {
    echo "invalid";
    exit();
}
?>
