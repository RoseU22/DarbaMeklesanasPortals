<?php
session_start();
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pazinojumi_id'])) {
    $pazinojumi_id = intval($_POST['pazinojumi_id']);

    if (!isset($_SESSION['userType'])) {
        echo "invalid_user";
        exit();
    }

    $userType = $_SESSION['userType'];
    $columnToUpdate = $userType === 'uznemums' ? 'uznemums_izdzesa' : 'klients_izdzesa';

    if ($userType === 'uznemums') {
        $sql = "UPDATE DMPortals_Pazinojumi SET $columnToUpdate = 1, statuss = 'Noraidīts' WHERE pazinojumi_id = ?";
    } else {
        $sql = "UPDATE DMPortals_Pazinojumi SET $columnToUpdate = 1 WHERE pazinojumi_id = ?";
    }
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

require 'con_db.php';

$checkSql = "SELECT uznemums_izdzesa, klients_izdzesa FROM DMPortals_Pazinojumi WHERE pazinojumi_id = ?";
$checkStmt = $savienojums->prepare($checkSql);
$checkStmt->bind_param("i", $pazinojumi_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row['uznemums_izdzesa'] == 1 && $row['klients_izdzesa'] == 1) {
        $deleteSql = "DELETE FROM DMPortals_Pazinojumi WHERE pazinojumi_id = ?";
        $deleteStmt = $savienojums->prepare($deleteSql);
        $deleteStmt->bind_param("i", $pazinojumi_id);
        $deleteStmt->execute();
        $deleteStmt->close();
    }
}

$checkStmt->close();
$savienojums->close();
?>
