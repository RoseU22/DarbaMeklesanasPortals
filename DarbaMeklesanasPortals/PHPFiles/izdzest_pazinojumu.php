<?php
session_start();
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pazinojumi_id'])) {
    $pazinojumi_id = intval($_POST['pazinojumi_id']);

    $sql = "DELETE FROM DMPortals_Pazinojumi WHERE pazinojumi_id = ?";
    $stmt = $savienojums->prepare($sql);
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
}
?>
