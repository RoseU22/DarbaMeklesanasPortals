<?php

session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['name'], $data['description'], $data['location'], $data['skills'], $data['salary'])) {
    require 'con_db.php';

    $username = $_SESSION['username']; // Get the logged-in user's name

    if (isset($data['id']) && $data['id'] !== null) {
        // Atjaunina eksistējošo vakanci
        $query = "UPDATE DMPortals_Vakances SET vakances_nosaukums = ?, vakances_apraksts = ?, atrasanas_vieta = ?, nepieciesamas_prasmes = ?, maksa = ? WHERE vakancesID = ?";
        if ($stmt = $savienojums->prepare($query)) {
            $stmt->bind_param("sssssi", $data['name'], $data['description'], $data['location'], $data['skills'], $data['salary'], $data['id']);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error executing the update query: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing the update query: ' . $savienojums->error]);
        }
    } else {
        // Izveido jaunu vakanci
        $query = "INSERT INTO DMPortals_Vakances (vakances_nosaukums, vakances_apraksts, atrasanas_vieta, nepieciesamas_prasmes, maksa, uznemuma_nosaukums) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = $savienojums->prepare($query)) {
            $stmt->bind_param("ssssss", $data['name'], $data['description'], $data['location'], $data['skills'], $data['salary'], $username);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error executing the insert query: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing the insert query: ' . $savienojums->error]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
}

?>
