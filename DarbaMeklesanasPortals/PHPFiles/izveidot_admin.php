<?php
session_start();

require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$userId || !$password) {
        die('Nepieciešama lietotāja ID un parole.');
    }
  
    $userId = intval($userId);
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "UPDATE DMPortals 
            SET loma = 'administrators', admin_parole = ? 
            WHERE lietotajsID = ?";

    if ($stmt = $savienojums->prepare($sql)) {
        $stmt->bind_param("si", $hashedPassword, $userId);

        if ($stmt->execute()) {
            
            header("Location: ../Admin/admin_panelis.php");
            exit();
        } else {
            echo "Kļūda atjaunināšanā: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Kļūda sagatavojot vaicājumu: " . $savienojums->error;
    }
} else {
    echo "Nepareizs pieprasījuma veids.";
}

$savienojums->close();
?>
