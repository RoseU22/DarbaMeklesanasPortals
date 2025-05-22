<?php
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lietotajsID'])) {
    $lietotajsID = $_POST['lietotajsID'];

    $sql = "UPDATE DMPortals SET statuss = NULL WHERE lietotajsID = ?";
    $stmt = $savienojums->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $lietotajsID);
        if ($stmt->execute()) {
            header("Location: ../Admin/admin_panelis.php");
            exit();
        } else {
            echo "Neizdevās aktivizēt lietotāju.";
        }
        $stmt->close();
    } else {
        echo "Kļūda vaicājumā.";
    }
} else {
    echo "Nepareizi dati.";
}
$savienojums->close();
?>
