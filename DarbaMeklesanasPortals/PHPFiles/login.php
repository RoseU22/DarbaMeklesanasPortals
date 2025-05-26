<?php
session_start();

header("Content-Type: application/json");

require 'con_db.php';

if (!$savienojums) {
    echo json_encode(["success" => false, "error" => "Neizdevās izveidot savienojumu ar datu bāzi"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["success" => false, "error" => "Nederīgs pieprasījums"]);
    exit;
}

if (!isset($_POST["userType"])) {
    echo json_encode(["success" => false, "error" => "Lietotāja tips nav norādīts"]);
    exit;
}

$userType = $_POST["userType"];
$username = trim($_POST["username"]);
$password = trim($_POST["password"]);


if ($userType === "klients"){
    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "error" => "Visi lauki ir jāaizpilda"]);
        exit;
    }
}

// Login klientiem
if ($userType === "klients") {
    $stmt = $savienojums->prepare("SELECT parole, loma FROM DMPortals WHERE lietotajvards = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Datu bāzes vaicājums neizdevās"]);
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $loma);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["username"] = $username;
            $_SESSION["userType"] = "klients";
            $_SESSION["loma"] = $loma;
            echo json_encode(["success" => true, "username" => $username, "userType" => "klients"]);
        } else {
            echo json_encode(["success" => false, "error" => "Nepareiza parole"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Lietotājs nav atrasts"]);
    }

    $stmt->close();
    exit;
}

// Login uzņēmumiem
if ($userType === "uznemums") {
    $company_name = trim($_POST["company_name"] ?? "");
    $company_password = trim($_POST["company_password"] ?? "");

    if ($userType === "uznemums"){
        if (empty($company_name) || empty($company_password)) {
            echo json_encode(["success" => false, "error" => "Visi lauki ir jāaizpilda"]);
            exit;
        }
    }

    // Get password and status from database
    $stmt = $savienojums->prepare("SELECT uznemuma_parole, statuss, apstiprinats FROM DMPortals_Uznemums WHERE uznemuma_nosaukums = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Datu bāzes vaicājums neizdevās"]);
        exit;
    }

    $stmt->bind_param("s", $company_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $status, $apstiprinats);
        $stmt->fetch();

        if ($apstiprinats !== "apstiprinats") {
            echo json_encode(["success" => false, "error" => "Uzņēmuma konts nav apstiprināts"]);
            $stmt->close();
            exit;
        }

        if (password_verify($company_password, $hashed_password)) {
            $_SESSION["username"] = $company_name;
            $_SESSION["userType"] = "uznemums";
            echo json_encode(["success" => true, "username" => $company_name, "userType" => "uznemums"]);
        } else {
            echo json_encode(["success" => false, "error" => "Nepareiza parole"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Uzņēmums nav atrasts"]);
    }

    $stmt->close();
    exit;
}

// Ja nav atrasts derīgs lietotāja tips
echo json_encode(["success" => false, "error" => "Nederīgs lietotāja tips"]);
exit;
