<?php
session_start();

header("Content-Type: application/json");

require 'con_db.php';

if (!$savienojums) {
    echo json_encode(["success" => false, "error" => "Neizdevās izveidot savienojumu ar datu bāzi"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST["userType"])) {
        echo json_encode(["success" => false, "error" => "Lietotāja tips nav norādīts"]);
        exit;
    }

    $userType = $_POST["userType"];

    if ($userType === "klients") {
        // Reģistrācija priekš "klients"
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
    } elseif ($userType === "uznemums") {
        // Reģistrācija priekš "uznemums"
        $companyName = trim($_POST["companyName"]);
        $regNumber = trim($_POST["regNumber"]);
        $companyEmail = trim($_POST["companyEmail"]);
        $phone = trim($_POST["phone"]);
        $vatNumber = trim($_POST["vatNumber"]);
        $companyPassword = trim($_POST["companyPassword"]);

        if (empty($companyName) || empty($regNumber) || empty($companyEmail) || empty($phone) || empty($vatNumber) || empty($companyPassword)) {
            echo json_encode(["success" => false, "error" => "Visi lauki ir jāaizpilda"]);
            exit;
        }

        $hashed_password = password_hash($companyPassword, PASSWORD_DEFAULT);

        $stmt = $savienojums->prepare("INSERT INTO DMPortals_Uznemums (uznemuma_nosaukums, registracijas_numurs, uznemuma_epasts, uznemuma_TelNr, PVN_numurs, uznemuma_parole) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $companyName, $regNumber, $companyEmail, $phone, $vatNumber, $hashed_password);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Kļūda reģistrējot uzņēmumu"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Nederīgs lietotāja tips"]);   
    }

    exit;
}

echo json_encode(["success" => false, "error" => "Invalid request"]);
exit;
