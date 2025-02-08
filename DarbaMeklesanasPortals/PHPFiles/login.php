<?php
session_start();

header("Content-Type: application/json"); 

require 'con_db.php'; 

if (!$savienojums) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);


    $stmt = $savienojums->prepare("SELECT parole FROM DMPortals WHERE lietotajvards = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Database query failed"]);
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();


    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();


        if (password_verify($password, $hashed_password)) {
            $_SESSION["username"] = $username; 
            echo json_encode(["success" => true, "username" => $username]); 
            exit;
        } else {
            echo json_encode(["success" => false, "error" => "Incorrect password"]); 
            exit;
        }
    } else {
        echo json_encode(["success" => false, "error" => "User not found"]); 
        exit;
    }
}


echo json_encode(["success" => false, "error" => "Invalid request"]);
exit;
