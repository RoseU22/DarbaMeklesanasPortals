<?php
session_start();
require 'con_db.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['userType'])) {
    exit();
}

$sessionUsername = $_SESSION['username'];
$userType = $_SESSION['userType'];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Invalid request.";
    exit();
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($email) || empty($password)) {
    echo "Visi lauki ir obligāti.";
    exit();
}

if ($userType === 'klients') {
    // Fetch user to verify credentials
    $stmt = $savienojums->prepare("SELECT lietotajsID, parole, epasts FROM DMPortals WHERE lietotajvards = ?");
    $stmt->bind_param("s", $sessionUsername);
} elseif ($userType === 'uznemums') {
    $stmt = $savienojums->prepare("SELECT uznemumsID, uznemuma_parole, uznemuma_epasts FROM DMPortals_Uznemums WHERE uznemuma_nosaukums = ?");
    $stmt->bind_param("s", $sessionUsername);
} else {
    echo "Nepareizs lietotāja tips.";
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Lietotājs nav atrasts.";
    exit();
}

$user = $result->fetch_assoc();
$correctEmail = $userType === 'klients' ? $user['epasts'] : $user['uznemuma_epasts'];
$correctPasswordHash = $userType === 'klients' ? $user['parole'] : $user['uznemuma_parole'];
$userID = $userType === 'klients' ? $user['lietotajsID'] : $user['uznemumsID'];

if ($email !== $correctEmail || !password_verify($password, $correctPasswordHash)) {
    echo "Nepareizi dati.";
    exit();
}

// Begin deletion
if ($userType === 'klients') {
    // Delete related data
    $del_cv = $savienojums->prepare("DELETE FROM DMPortals_CV WHERE lietotajvards = ?");
    $del_cv->bind_param("s", $sessionUsername);
    $del_cv->execute();

    $del_pazinojumi = $savienojums->prepare("DELETE FROM DMPortals_Pazinojumi WHERE klients_id = ?");
    $del_pazinojumi->bind_param("i", $userID);
    $del_pazinojumi->execute();

    $del_user = $savienojums->prepare("DELETE FROM DMPortals WHERE lietotajvards = ?");
    $del_user->bind_param("s", $sessionUsername);
    $del_user->execute();

} elseif ($userType === 'uznemums') {
    // First delete related vacancies
    $del_vacancies = $savienojums->prepare("SELECT vakancesID FROM DMPortals_Vakances WHERE uznemuma_nosaukums = ?");
    $del_vacancies->bind_param("s", $sessionUsername);
    $del_vacancies->execute();
    $vacancies_result = $del_vacancies->get_result();

    while ($row = $vacancies_result->fetch_assoc()) {
        $vakance_id = $row['vakancesID'];
        $del_paz = $savienojums->prepare("DELETE FROM DMPortals_Pazinojumi WHERE vakance_id = ?");
        $del_paz->bind_param("i", $vakance_id);
        $del_paz->execute();
    }

    $del_vacancies_final = $savienojums->prepare("DELETE FROM DMPortals_Vakances WHERE uznemuma_nosaukums = ?");
    $del_vacancies_final->bind_param("s", $sessionUsername);
    $del_vacancies_final->execute();

    $del_user = $savienojums->prepare("DELETE FROM DMPortals_Uznemums WHERE uznemuma_nosaukums = ?");
    $del_user->bind_param("s", $sessionUsername);
    $del_user->execute();
}

// Iznīvina sessiju
session_unset();
session_destroy();

echo "success";
exit();
