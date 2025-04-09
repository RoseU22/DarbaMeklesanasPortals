<?php
require 'con_db.php';

if (isset($_GET['id'])) {
    $cvId = $_GET['id'];

    // Iegūsta CV datus un valodu no datu bāzes
    $query = "SELECT * FROM DMPortals_CV WHERE id = ?";
    $stmt = $savienojums->prepare($query);
    $stmt->bind_param("i", $cvId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $cv = $result->fetch_assoc();
            echo json_encode(['success' => true, 'cv' => $cv, 'language' => $cv['valoda']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'CV nav atrasts.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Kļūda iegūstot CV datus.']);
    }

    $stmt->close();
}
?>
