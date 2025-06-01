<?php
require '../PHPFiles/con_db.php';
session_start();

if (!isset($_GET['token'])) {
    die('Kļūda: nav norādīts tokens.');
}

$token = $_GET['token'];

$stmt = $savienojums->prepare("SELECT konta_tips, konta_nosaukums, epasts, derigs_lidz FROM DMPortals_parolesAtjaunosana WHERE tokens = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

// Pārbauda vai tokens joprojām pastāv un ir derīgs
if (!$result || $result->num_rows !== 1) {
    header("Location: ../index.php");
    exit();
}

$row = $result->fetch_assoc();
$expires = strtotime($row['derigs_lidz']);
if ($expires < time()) {
    die('Paroles atiestatīšanas tokens ir beidzies.');
}

$userType = $row['konta_tips'];
$email = $row['epasts'];
?>

<!DOCTYPE html>
<html lang="lv">

<head>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Paroles atjaunošana</title>

    <link rel="shortcut icon" href="../Bildes/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="restartetParoli.css">

    <script>
        const userType = <?php echo json_encode($userType); ?>;
        const email = <?php echo json_encode($email); ?>;
        const token = <?php echo json_encode($token); ?>;
    </script>
    <script src="restartet_paroli.js"></script>
    <script src="../BurbuluAnimacija.js"></script>

</head>

<body>

    <div class="burbulu-background"></div>

    <div class="modal-bg">
        <div class="modal">
            <h2>Paroles atjaunošana</h2>
            <form id="resetForm">
                <label for="newPassword">Jaunā parole:</label>
                <input type="password" id="newPassword" name="newPassword" required minlength="6" />
                
                <label for="confirmNewPassword">Apstipriniet jauno paroli:</label>
                <input type="password" id="confirmNewPassword" name="confirmNewPassword" required minlength="6" />
                
                <button type="submit">Nomainīt paroli</button>
                <script>if ( window.history.replaceState ) {
                    window.history.replaceState( null, null, window.location.href );}
                </script>
            </form>
            <div id="message"></div>
        </div>
    </div>

</body>
</html>
