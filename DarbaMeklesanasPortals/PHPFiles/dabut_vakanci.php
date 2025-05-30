<?php
require 'con_db.php';

if (isset($_GET['id'])) {
    $vakanceId = $_GET['id'];

    $query = "SELECT * FROM DMPortals_Vakances WHERE vakancesID = ?";
    $stmt = $savienojums->prepare($query);
    $stmt->bind_param("i", $vakanceId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $vakance = $result->fetch_assoc();

            unset($vakance['bilde']);

            echo json_encode(['success' => true, 'vakance' => $vakance]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Vakance nav atrasta.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Kļūda iegūstot vakances datus.']);
    }

    $stmt->close();
}
?>
