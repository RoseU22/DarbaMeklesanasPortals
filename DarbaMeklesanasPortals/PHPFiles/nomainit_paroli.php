<?php
require 'con_db.php';
header('Content-Type: text/plain');

if (!isset($_POST['userType'], $_POST['email'], $_POST['oldPassword'], $_POST['newPassword'], $_POST['confirmNewPassword'])) {
    echo "Trūkst nepieciešamie dati vai konta tips nav izvēlēts!";
    exit();
}

$userType = $_POST['userType'];
$email = $_POST['email'];
$oldPassword = $_POST['oldPassword'];
$newPassword = $_POST['newPassword'];
$confirmNewPassword = $_POST['confirmNewPassword'];

if ($newPassword !== $confirmNewPassword) {
    echo "Jaunās paroles nesakrīt!";
    exit();
}

if ($userType === "klients") {
    $query = "SELECT parole FROM DMPortals WHERE epasts = ?";
    $update = "UPDATE DMPortals SET parole = ? WHERE epasts = ?";
} elseif ($userType === "uznemums") {
    $query = "SELECT uznemuma_parole FROM DMPortals_Uznemums WHERE uznemuma_epasts = ?";
    $update = "UPDATE DMPortals_Uznemums SET uznemuma_parole = ? WHERE uznemuma_epasts = ?";
} else {
    echo "Nederīgs konta tips!";
    exit();
}

$stmt = $savienojums->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $storedPassword = ($userType === "klients") ? $row['parole'] : $row['uznemuma_parole'];

    if (!password_verify($oldPassword, $storedPassword)) {
        echo "Nepareiza vecā parole!";
        exit();
    }

    if (password_verify($newPassword, $storedPassword)) {
        echo "Jaunā parole nedrīkst būt tāda pati kā vecā parole!";
        exit();
    }

    $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $savienojums->prepare($update);
    $stmt->bind_param("ss", $newHashed, $email);

    if ($stmt->execute()) {
        echo "Parole veiksmīgi nomainīta!";
    } else {
        echo "Kļūda saglabājot jauno paroli.";
    }

} else {
    echo "Lietotājs ar šādu e-pastu nav atrasts!";
}
?>
