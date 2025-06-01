<?php
session_start();
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vakancesID = $_POST['vakancesID'];

    if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'uznemums') {
        http_response_code(403);
        exit("Nav piekļuves tiesību.");
    }

    // Pārbauda vai nav augšupielādēts jauns attēls
    if (isset($_FILES['vacancyImage']) && $_FILES['vacancyImage']['error'] === 0) {
        // Augšupielādēts jauns attēls
        $bildeData = file_get_contents($_FILES['vacancyImage']['tmp_name']);

        
        $query = "UPDATE DMPortals_Vakances SET bilde = ? WHERE vakancesID = ?";
        $stmt = $savienojums->prepare($query);

        
        $stmt->bind_param("bi", $null, $vakancesID);
        $null = NULL; 
        $stmt->send_long_data(0, $bildeData);

        $executed = $stmt->execute();
        $stmt->close();

        if ($executed) {
            echo "Vakance saglabāta ar jauno attēlu.";
        } else {
            echo "Kļūda, saglabājot jauno attēlu: " . $stmt->error;
        }
    } else {
        echo "Attēls netika mainīts, saglabātas citas izmaiņas (ja ir).";
    }

    $savienojums->close();
}
?>
