<?php
session_start();
header('Content-Type: application/json');
require 'con_db.php';

// Enable error reporting for debugging (REMOVE IN PRODUCTION)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'klients') {
    echo json_encode(['success' => false, 'message' => 'Nepieciešama klienta autorizācija.']);
    exit;
}

// Fetch the klients_id using the session username
$klients_username = $_SESSION['username'];
$klients_id = null;

$stmt = $savienojums->prepare("SELECT lietotajsID FROM DMPortals WHERE lietotajvards = ?");
if ($stmt) {
    $stmt->bind_param("s", $klients_username);
    $stmt->execute();
    $stmt->bind_result($klients_id);
    $stmt->fetch();
    $stmt->close();
}

if (!$klients_id) {
    echo json_encode(['success' => false, 'message' => 'Klienta ID nav atrasts.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vakance_id = $_POST['vakance_id'] ?? null;
    $cv_id = $_POST['cv_id'] ?? null;

    // Debugging output for variables
    error_log("klients_id: " . var_export($klients_id, true));
    error_log("vakance_id: " . var_export($vakance_id, true));
    error_log("cv_id: " . var_export($cv_id, true));

    if (!$vakance_id || !$cv_id) {
        echo json_encode(['success' => false, 'message' => 'Trūkst nepieciešamie dati.']);
        exit;
    }

    // Validate that the provided values are integers
    if (!is_numeric($vakance_id) || !is_numeric($cv_id)) {
        echo json_encode(['success' => false, 'message' => 'Nepareizi dati (vakance vai CV ID).']);
        exit;
    }

    // Prepare and execute insert query
    $stmt = $savienojums->prepare("INSERT INTO DMPortals_Pazinojumi (vakance_id, klients_id, cv_id) VALUES (?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Kļūda sagatavojot vaicājumu: ' . $savienojums->error]);
        exit;
    }

    $stmt->bind_param("iii", $vakance_id, $klients_id, $cv_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pieteikums veiksmīgi nosūtīts!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Neizdevās saglabāt pieteikumu: ' . $stmt->error]);
    }

    $stmt->close();
    $savienojums->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Nederīgs pieprasījuma veids.']);
}
?>
