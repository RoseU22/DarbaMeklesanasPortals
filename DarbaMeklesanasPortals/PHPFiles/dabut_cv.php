<?php
require 'con_db.php';

if (isset($_GET['id'])) {
    $cvId = $_GET['id'];

    // Fetch the CV data and the language from the database
    $query = "SELECT * FROM DMPortals_CV WHERE id = ?";
    $stmt = $savienojums->prepare($query);
    $stmt->bind_param("i", $cvId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $cv = $result->fetch_assoc();
            echo json_encode(['success' => true, 'cv' => $cv, 'language' => $cv['valoda']]); // include 'valoda' in the response
        } else {
            echo json_encode(['success' => false, 'message' => 'CV not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching CV data.']);
    }

    $stmt->close();
}
?>
