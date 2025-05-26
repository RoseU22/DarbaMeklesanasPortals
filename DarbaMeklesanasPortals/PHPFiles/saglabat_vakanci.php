<?php

session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Lietotājs nav ielogojies']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Pārbaude vai visi nepieciešamie lauki ir pieejami
if (isset($data['name'], $data['description'], $data['country'], $data['city'], $data['street'], $data['skills'], $data['salary'])) {
    require 'con_db.php';

    $username = $_SESSION['username'];

    if (isset($data['id']) && $data['id'] !== null) {
        // Atjauno esošu ierakstu
        $query = "UPDATE DMPortals_Vakances SET vakances_nosaukums = ?, vakances_apraksts = ?, valsts = ?, pilseta = ?, iela = ?, nepieciesamas_prasmes = ?, maksa = ? WHERE vakancesID = ?";
        if ($stmt = $savienojums->prepare($query)) {
            $stmt->bind_param("ssssssdi", $data['name'], $data['description'], $data['country'], $data['city'], $data['street'], $data['skills'], $data['salary'], $data['id']);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Kļūda, izpildot atjaunināšanas vaicājumu: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Sagatavojot atjaunināšanas vaicājumu, radās kļūda: ' . $savienojums->error]);
        }
    } else {
        // Ievieto jaunu vakanci
        $query = "INSERT INTO DMPortals_Vakances (vakances_nosaukums, vakances_apraksts, valsts, pilseta, iela, nepieciesamas_prasmes, maksa, uznemuma_nosaukums) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $savienojums->prepare($query)) {
            $stmt->bind_param("ssssssds", $data['name'], $data['description'], $data['country'], $data['city'], $data['street'], $data['skills'], $data['salary'], $username);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Kļūda, izpildot ievietošanas vaicājumu: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Kļūda, sagatavojot ievietošanas vaicājumu: ' . $savienojums->error]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Trūkst obligāto lauku']);
}

?>
