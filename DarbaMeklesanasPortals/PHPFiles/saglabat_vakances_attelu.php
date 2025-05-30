<?php
session_start();
require 'con_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vakancesID = $_POST['vakancesID'];

    if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'uznemums') {
        http_response_code(403);
        exit("Nav piekļuves tiesību.");
    }

    // Check if a new image is uploaded
    if (isset($_FILES['vacancyImage']) && $_FILES['vacancyImage']['error'] === 0) {
        // New image uploaded, read its content
        $bildeData = file_get_contents($_FILES['vacancyImage']['tmp_name']);

        // Prepare update query to change image
        $query = "UPDATE DMPortals_Vakances SET bilde = ? WHERE vakancesID = ?";
        $stmt = $savienojums->prepare($query);

        // For blob data, bind_param with "b" and use send_long_data()
        $stmt->bind_param("bi", $null, $vakancesID);
        $null = NULL; // dummy variable needed for bind_param
        $stmt->send_long_data(0, $bildeData);

        $executed = $stmt->execute();
        $stmt->close();

        if ($executed) {
            echo "Vakance saglabāta ar jauno attēlu.";
        } else {
            echo "Kļūda, saglabājot jauno attēlu: " . $stmt->error;
        }
    } else {
        // No new image uploaded, so do NOT update the image column,
        // but you might want to update other vacancy fields here if needed.
        echo "Attēls netika mainīts, saglabātas citas izmaiņas (ja ir).";
    }

    $savienojums->close();
}
?>
