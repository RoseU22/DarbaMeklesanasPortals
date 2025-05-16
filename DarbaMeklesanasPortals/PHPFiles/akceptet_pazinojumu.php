<?php
session_start();
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pazinojumi_id'])) {
    $pazinojumi_id = intval($_POST['pazinojumi_id']);

    if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'uznemums') {
        echo "invalid_user";
        exit();
    }

    $sql = "UPDATE DMPortals_Pazinojumi SET statuss = 'Akceptēts' WHERE pazinojumi_id = ?";
    $stmt = $savienojums->prepare($sql);
    if (!$stmt) {
        echo "prepare_error";
        exit();
    }

    $stmt->bind_param("i", $pazinojumi_id);
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
