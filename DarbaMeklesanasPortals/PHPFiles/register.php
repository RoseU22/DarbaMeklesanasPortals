<?php
session_start();

header("Content-Type: application/json");

require 'con_db.php'; 

if (!$savienojums) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $lietotajvards = trim($_POST["lietotajvards"]);
    $vards = trim($_POST["vards"]);
    $uzvards = trim($_POST["uzvards"]);
    $parole = trim($_POST["parole"]);
    $epasts = trim($_POST["epasts"]);

    if (empty($lietotajvards) || empty($vards) || empty($uzvards) || empty($parole) || empty($epasts)) {
        echo json_encode(["success" => false, "error" => "Visi lauki ir jāaizpilda"]);
        exit;
    }

    $stmt = $savienojums->prepare("SELECT lietotajvards FROM DMPortals WHERE lietotajvards = ?");
    $stmt->bind_param("s", $lietotajvards);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "Šis lietotājvārds jau ir aizņemts"]);
        exit;
    }

    $hashed_password = password_hash($parole, PASSWORD_DEFAULT);

    $stmt = $savienojums->prepare("INSERT INTO DMPortals (vards, uzvards, parole, epasts, lietotajvards) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $vards, $uzvards, $hashed_password, $epasts, $lietotajvards);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Kļūda reģistrējot kontu"]);
    }

    $stmt->close();
    exit;
}

echo json_encode(["success" => false, "error" => "Invalid request"]);
exit;
