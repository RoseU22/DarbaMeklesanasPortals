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

// Check if this is a reset (token given), else normal change
$isReset = !empty($token);

// If reset, verify token is valid and get user info from token table
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
    // Override userType and email with DB values to avoid spoofing
    $userType = $row['konta_tips'];
    $email = $row['epasts'];

    // Now fetch the stored password hash from the appropriate user table:
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
    // Normal password change - verify old password
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
    // For reset, set update query based on userType
    if ($userType === "klients") {
        $update = "UPDATE DMPortals SET parole = ? WHERE epasts = ?";
    } elseif ($userType === "uznemums") {
        $update = "UPDATE DMPortals_Uznemums SET uznemuma_parole = ? WHERE uznemuma_epasts = ?";
    } else {
        echo "Nederīgs konta tips!";
        exit();
    }
}

// Now update the password
$newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $savienojums->prepare($update);
$stmt->bind_param("ss", $newHashed, $email);

if ($stmt->execute()) {
    if ($isReset) {
        // Delete used token
        $delStmt = $savienojums->prepare("DELETE FROM DMPortals_parolesAtjaunosana WHERE tokens = ?");
        $delStmt->bind_param("s", $token);
        $delStmt->execute();
    }
    echo "Parole veiksmīgi nomainīta!";
} else {
    echo "Kļūda saglabājot jauno paroli.";
}
?>
