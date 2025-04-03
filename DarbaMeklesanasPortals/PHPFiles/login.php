<?php
session_start();

header("Content-Type: application/json");

require 'con_db.php';

if (!$savienojums) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["userType"])) {
        echo json_encode(["success" => false, "error" => "Lietotāja tips nav norādīts"]);
        exit;
    }

    $userType = $_POST["userType"];
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $company_password = isset($_POST["company_password"]) ? trim($_POST["company_password"]) : null;
    $company_name = isset($_POST["company_name"]) ? trim($_POST["company_name"]) : null;

    // Ensure that necessary fields are filled
    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "error" => "Visi lauki ir jāaizpilda"]);
        exit;
    }

    if ($userType === "klients") {
        // Login for clients
        $stmt = $savienojums->prepare("SELECT parole FROM DMPortals WHERE lietotajvards = ?");
    } elseif ($userType === "uznemums") {
        // Login for companies (company name check)
        if (empty($company_name)) {
            echo json_encode(["success" => false, "error" => "Uzņēmuma nosaukums ir jānorāda"]);
            exit;
        }

        $stmt = $savienojums->prepare("SELECT uznemuma_parole FROM DMPortals_Uznemums WHERE uznemuma_nosaukums = ?");
    } else {
        echo json_encode(["success" => false, "error" => "Nederīgs lietotāja tips"]);
        exit;
    }

    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Database query failed"]);
        exit;
    }

    if ($userType === "klients") {
        $stmt->bind_param("s", $username);
    } elseif ($userType === "uznemums") {
        $stmt->bind_param("s", $company_name);
    }

    $stmt->execute();
    $stmt->store_result();

    // Debugging: Check the number of rows found
    error_log("Number of rows: " . $stmt->num_rows);

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Debugging: Output the company name and password to verify correctness
        error_log("Company Name: " . $company_name);
        error_log("Fetched Password: " . $hashed_password);

        // Check password for client login
        if ($userType === "klients") {
            if (password_verify($password, $hashed_password)) {
                $_SESSION["username"] = $username;
                $_SESSION["userType"] = $userType;
                echo json_encode(["success" => true, "username" => $username, "userType" => $userType]);
            } else {
                echo json_encode(["success" => false, "error" => "Nepareiza parole"]);
            }
        }

        // Check password for company login
        elseif ($userType === "uznemums") {
            if (password_verify($company_password, $hashed_password)) {
                $_SESSION["username"] = $company_name;
                $_SESSION["userType"] = $userType;
                echo json_encode(["success" => true, "username" => $company_name, "userType" => $userType]);
            } else {
                echo json_encode(["success" => false, "error" => "Nepareiza parole"]);
            }
        }

    } else {
        // Debugging: No rows found, print the company name for inspection
        error_log("No rows found for company: " . $company_name);
        echo json_encode(["success" => false, "error" => "Lietotājs nav atrasts"]);
    }

    $stmt->close();
    exit;
}

echo json_encode(["success" => false, "error" => "Invalid request"]);
exit;
