<?php
session_start();
require 'con_db.php';

function logAdminAction($savienojums, $admin_id, $admin_lietotajvards, $apraksts) {
    $stmt = $savienojums->prepare("INSERT INTO DMPortals_AdminLog (admin_id, admin_lietotajvards, apraksts, laiks) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $admin_id, $admin_lietotajvards, $apraksts);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$userId || !$password) {
        die('Nepieciešama lietotāja ID un parole.');
    }

    $userId = intval($userId);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $savienojums->prepare("SELECT lietotajvards FROM DMPortals WHERE lietotajsID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($targetUsername);
    if (!$stmt->fetch()) {
        echo "Neizdevās atrast lietotāju.";
        exit();
    }
    $stmt->close();

    $sql = "UPDATE DMPortals SET loma = 'administrators', admin_parole = ? WHERE lietotajsID = ?";
    if ($stmt = $savienojums->prepare($sql)) {
        $stmt->bind_param("si", $hashedPassword, $userId);
        if ($stmt->execute()) {
            $stmt->close();
            
            if (isset($_SESSION['username'])) {
                $admin_lietotajvards = $_SESSION['username'];

                $stmt = $savienojums->prepare("SELECT lietotajsID FROM DMPortals WHERE lietotajvards = ?");
                $stmt->bind_param("s", $admin_lietotajvards);
                $stmt->execute();
                $stmt->bind_result($admin_id);
                if ($stmt->fetch()) {
                    $stmt->close();

                    $apraksts = "Pārtaisīja '$targetUsername' par administratoru";
                    logAdminAction($savienojums, $admin_id, $admin_lietotajvards, $apraksts);
                } else {
                    echo "Neizdevās atrast administratora informāciju logam.";
                }
            }

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
