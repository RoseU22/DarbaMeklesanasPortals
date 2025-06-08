<?php
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vakancesID'])) {
    $vakancesID = intval($_POST['vakancesID']);

    // Update instead of deleting
    $stmt = $savienojums->prepare("UPDATE DMPortals_Vakances SET dzests = 'dzests' WHERE vakancesID = ?");
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
