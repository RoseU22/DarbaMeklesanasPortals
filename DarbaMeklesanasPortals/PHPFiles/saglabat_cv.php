<?php

session_start();


if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}


$data = json_decode(file_get_contents('php://input'), true);


if (isset($data['name'], $data['email'], $data['phone'], $data['address'], $data['dob'], $data['education'], $data['workExperience'], $data['skills'], $data['languages'], $data['additionalInfo'], $data['username'], $data['language'])) {
    

    require 'con_db.php';


    $query = "INSERT INTO DMPortals_CV (lietotajvards, vards, epasts, talrunis, adresse, gads, izglitiba, darba_pieredze, prasmes, valodas, papildus_info, valoda) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $savienojums->prepare($query)) {
        $stmt->bind_param("ssssssssssss", 
            $data['username'], 
            $data['name'], 
            $data['email'], 
            $data['phone'], 
            $data['address'], 
            $data['dob'], 
            $data['education'], 
            $data['workExperience'], 
            $data['skills'], 
            $data['languages'], 
            $data['additionalInfo'],
            $data['language']
        );


        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {

            echo json_encode(['success' => false, 'message' => 'Error executing the SQL query: ' . $stmt->error]);
        }
        $stmt->close();
    } else {

        echo json_encode(['success' => false, 'message' => 'Error preparing the SQL query: ' . $savienojums->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
}
?>
