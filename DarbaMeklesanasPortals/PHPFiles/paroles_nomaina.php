<?php
require 'con_db.php';
header('Content-Type: text/plain');

if (!isset($_POST['userType'], $_POST['accountName'], $_POST['email'])) {
    echo "Trūkst nepieciešamie dati!";
    exit();
}

$userType = $_POST['userType'];
$accountName = trim($_POST['accountName']);
$email = trim($_POST['email']);

// Atroda lietotāja tipu
if ($userType === "klients") {
    $query = "SELECT lietotajvards, epasts FROM DMPortals WHERE lietotajvards = ? AND epasts = ?";
} elseif ($userType === "uznemums") {
    $query = "SELECT uznemuma_nosaukums, uznemuma_epasts FROM DMPortals_Uznemums WHERE uznemuma_nosaukums = ? AND uznemuma_epasts = ?";
} else {
    echo "Nederīgs konta tips!";
    exit();
}

$stmt = $savienojums->prepare($query);
$stmt->bind_param("ss", $accountName, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    // Ja eksistē lietotājs tad izveido tokenu
    $token = bin2hex(random_bytes(16)); // secure token
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Ieliek token kopā ar citiem datiem datubāzē
    $insertQuery = "INSERT INTO DMPortals_parolesAtjaunosana (konta_tips, konta_nosaukums, epasts, tokens, derigs_lidz) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $savienojums->prepare($insertQuery);
    $insertStmt->bind_param("sssss", $userType, $accountName, $email, $token, $expires);
    $insertStmt->execute();

    // Uztaisa atjaunošanas linku
    $resetLink = "https://kristovskis.lv/3pt1/roze/DarbaMeklesanasPortals/ParolesMaina/restartet_paroli.php?token=" . $token;

    // Aizsūta epastu ar linku
    $subject = "Paroles atiestatīšanas pieprasījums";
    $message = "Sveiki, \n\nLūdzu, noklikšķiniet uz saites, lai nomainītu savu paroli:\n\n" . $resetLink . "\n\nSaite ir derīga 1 stundu.\n\nJa Jūs neesat pieprasījis paroles nomaiņu, ignorējiet šo e-pastu.";
    $headers = "From: noreply@yourdomain.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "Paroles atjaunošanas saite veiksmīgi nosūtīta uz Jūsu e-pastu.";
    } else {
        echo "Neizdevās nosūtīt e-pastu. Lūdzu, mēģiniet vēlreiz.";
    }
} else {
    echo "Lietotājs ar norādīto vārdu vai e-pastu netika atrasts.";
}
?>
