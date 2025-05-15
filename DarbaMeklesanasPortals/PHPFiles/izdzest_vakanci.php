<?php
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vakancesID'])) {
    $vakancesID = intval($_POST['vakancesID']);

    $stmt = $savienojums->prepare("DELETE FROM DMPortals_Vakances WHERE vakancesID = ?");
    $stmt->bind_param("i", $vakancesID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $savienojums->close();
} else {
    echo json_encode(['success' => false]);
}
