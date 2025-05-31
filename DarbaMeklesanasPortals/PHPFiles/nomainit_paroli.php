<?php
require 'con_db.php';
header('Content-Type: text/plain');

if (!isset($_POST['userType'], $_POST['email'], $_POST['newPassword'], $_POST['confirmNewPassword'])) {
    echo "Trūkst nepieciešamie dati vai konta tips nav izvēlēts!";
    exit();
}

$userType = $_POST['userType'];
$email = $_POST['email'];
$newPassword = $_POST['newPassword'];
$confirmNewPassword = $_POST['confirmNewPassword'];
$oldPassword = $_POST['oldPassword'] ?? '';
$token = $_POST['token'] ?? '';

if ($newPassword !== $confirmNewPassword) {
    echo "Jaunās paroles nesakrīt!";
    exit();
}


$isReset = !empty($token);

// ja atjauno, pārbauda, vai token ir derīgs, un iegūsta lietotāja informāciju no datubāzes
if ($isReset) {
    $stmt = $savienojums->prepare("SELECT konta_tips, epasts FROM DMPortals_parolesAtjaunosana WHERE tokens = ? AND derigs_lidz > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result || $result->num_rows !== 1) {
        echo "Nederīgs vai beidzies paroles atiestatīšanas tokens.";
        exit();
    }
    $row = $result->fetch_assoc();
    $userType = $row['konta_tips'];
    $email = $row['epasts'];

    if ($userType === "klients") {
        $query = "SELECT parole FROM DMPortals WHERE epasts = ?";
    } elseif ($userType === "uznemums") {
        $query = "SELECT uznemuma_parole FROM DMPortals_Uznemums WHERE uznemuma_epasts = ?";
    } else {
        echo "Nederīgs konta tips!";
        exit();
    }

    $stmtUser = $savienojums->prepare($query);
    $stmtUser->bind_param("s", $email);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();

    if ($resultUser && $resultUser->num_rows === 1) {
        $userRow = $resultUser->fetch_assoc();
        $storedPassword = ($userType === "klients") ? $userRow['parole'] : $userRow['uznemuma_parole'];

        if (password_verify($newPassword, $storedPassword)) {
            echo "Jaunā parole nedrīkst būt tāda pati kā vecā parole!";
            exit();
        }
    } else {
        echo "Lietotājs ar šādu e-pastu nav atrasts!";
        exit();
    }
}

if (!$isReset) {
    if (!isset($_POST['oldPassword']) || empty($oldPassword)) {
        echo "Lūdzu, ievadiet veco paroli!";
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
    } else {
        echo "Lietotājs ar šādu e-pastu nav atrasts!";
        exit();
    }

} else {
    if ($userType === "klients") {
        $update = "UPDATE DMPortals SET parole = ? WHERE epasts = ?";
    } elseif ($userType === "uznemums") {
        $update = "UPDATE DMPortals_Uznemums SET uznemuma_parole = ? WHERE uznemuma_epasts = ?";
    } else {
        echo "Nederīgs konta tips!";
        exit();
    }
}

$newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $savienojums->prepare($update);
$stmt->bind_param("ss", $newHashed, $email);

if ($stmt->execute()) {
    if ($isReset) {
        // Izdzēš VISUS tokenus šim lietotājam, lai nepieļautu atkārtotu lietošanu
        $deleteTokens = $savienojums->prepare("DELETE FROM DMPortals_parolesAtjaunosana WHERE epasts = ?");
        $deleteTokens->bind_param("s", $email);
        $deleteTokens->execute();
    }
    
    echo "Parole veiksmīgi nomainīta!";
} else {
    echo "Kļūda saglabājot jauno paroli.";
}
?>
