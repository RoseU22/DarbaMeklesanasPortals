<?php
session_start();
require 'PHPFiles/con_db.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['userType'])) {
    header("Location: index.php");
    exit();
}

$lietotajvards = $_SESSION['username'];
$user_type = $_SESSION['userType'];

if ($user_type === 'klients') {
    $table = 'DMPortals';
    $id_column = 'lietotajvards';
} elseif ($user_type === 'uznemums') {
    $table = 'DMPortals_Uznemums';
    $id_column = 'uznemuma_nosaukums';
} else {
    die("Nepareizs lietotāja tips.");
}

$stmt = $savienojums->prepare("SELECT * FROM $table WHERE $id_column = ?");
$stmt->bind_param("s", $lietotajvards);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Lietotājs nav atrasts.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    // Saglabā bildi uz datubāzi (LONGBLOB)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $image_data = file_get_contents($_FILES['profile_image']['tmp_name']);

        // Saglabā bildi attiecīgā datubāzē balstoties uz konta tipu
        if ($user_type === 'klients') {
            $stmt = $savienojums->prepare("UPDATE DMPortals SET profila_bilde = ? WHERE lietotajvards = ?");
            $stmt->bind_param("bs", $image_data, $lietotajvards);
        } else {
            $stmt = $savienojums->prepare("UPDATE DMPortals_Uznemums SET profila_bilde = ? WHERE uznemuma_nosaukums = ?");
            $stmt->bind_param("bs", $image_data, $lietotajvards);
        }

        $stmt->send_long_data(0, $image_data);
        $stmt->execute();
    }

    $changes_made = false;

    if ($user_type === 'klients') {
        $new_username = $_POST["lietotajvards"];
        $vards = $_POST["vards"];
        $uzvards = $_POST["uzvards"];
        $epasts = $_POST["epasts"];
        $parole = $_POST["parole"];

        if ($new_username !== $lietotajvards) {
            $check_stmt = $savienojums->prepare("SELECT lietotajvards FROM DMPortals WHERE lietotajvards = ?");
            $check_stmt->bind_param("s", $new_username);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                echo "<script>alert('Šis lietotājvārds jau ir aizņemts.'); window.location.href='profils.php';</script>";
                exit();
            }
        }

        $hashed_password = !empty($parole) ? password_hash($parole, PASSWORD_DEFAULT) : $user['parole'];

        $changes_made = (
            $new_username !== $user['lietotajvards'] ||
            $vards !== $user['vards'] ||
            $uzvards !== $user['uzvards'] ||
            $epasts !== $user['epasts'] ||
            (!empty($parole) && !password_verify($parole, $user['parole']))
        );

        if ($changes_made) {
            $stmt = $savienojums->prepare("UPDATE DMPortals SET lietotajvards = ?, vards = ?, uzvards = ?, epasts = ?, parole = ? WHERE lietotajvards = ?");
            $stmt->bind_param("ssssss", $new_username, $vards, $uzvards, $epasts, $hashed_password, $lietotajvards);
        }

    } elseif ($user_type === 'uznemums') {
        $new_username = $_POST["uznemuma_nosaukums"];
        $regnumurs = $_POST["registracijas_numurs"];
        $epasts = $_POST["uznemuma_epasts"];
        $telnr = $_POST["uznemuma_TelNr"];
        $pvn = $_POST["PVN_numurs"];
        $parole = $_POST["uznemuma_parole"];

        if ($new_username !== $lietotajvards) {
            $check_stmt = $savienojums->prepare("SELECT uznemuma_nosaukums FROM DMPortals_Uznemums WHERE uznemuma_nosaukums = ?");
            $check_stmt->bind_param("s", $new_username);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                echo "<script>alert('Šis uzņēmuma nosaukums jau ir aizņemts.'); window.location.href='profils.php';</script>";
                exit();
            }
        }

        $hashed_password = !empty($parole) ? password_hash($parole, PASSWORD_DEFAULT) : $user['uznemuma_parole'];

        $changes_made = (
            $new_username !== $user['uznemuma_nosaukums'] ||
            $regnumurs !== $user['registracijas_numurs'] ||
            $epasts !== $user['uznemuma_epasts'] ||
            $telnr !== $user['uznemuma_TelNr'] ||
            $pvn !== $user['PVN_numurs'] ||
            (!empty($parole) && !password_verify($parole, $user['uznemuma_parole']))
        );

        if ($changes_made) {
            $stmt = $savienojums->prepare("UPDATE DMPortals_Uznemums SET uznemuma_nosaukums = ?, registracijas_numurs = ?, uznemuma_epasts = ?, uznemuma_TelNr = ?, PVN_numurs = ?, uznemuma_parole = ? WHERE uznemuma_nosaukums = ?");
            $stmt->bind_param("sssssss", $new_username, $regnumurs, $epasts, $telnr, $pvn, $hashed_password, $lietotajvards);
        }
    }

    // Parāda paziņojumu, ja izmaiņas tika veiktas
    if ($changes_made) {
        if ($stmt->execute()) {
            $_SESSION['username'] = $new_username;
            echo "<script>alert('Profils atjaunināts!'); window.location.href='profils.php';</script>";
            exit();
        } else {
            echo "Kļūda saglabājot datus.";
        }
    }

}

?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Profila rediģēšana</title>
    <link rel="shortcut icon" href="Bildes/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="profils.css">
    <link rel="stylesheet" href="style.css">
    <script src="autorizacija.js"></script>
    <script src="profils.js"></script>
    <script src="dzest_profilu.js"></script>
</head>
<body>
    <header>
        <div class="konteiners">
            <div class="header-left">

                <a href="index.php">

                    <img src="Bildes/Favicon.png" alt="Logo" class="header-logo">
                    <h1>Darba Meklēšanas Portāls</h1>

                </a>
                
            </div>
            <nav>
                <ul>
                    <li><a href="#Par">Par portālu</a></li>
                    <li><a href="#features">Funkcionalitātes</a></li>
                    <li><a href="#Darbi">Darba piedāvājumi</a></li>
                    <li><a href="#Pieteikties">Pieteikties</a></li>
                    <li><a href="#Kontakti">Kontakti</a></li>
                </ul>
            </nav>
            <div>
                <?php if (isset($_SESSION["username"])): ?>
                    <div class="profile-container">
                        <p class="profile-btn" id="profileDropdownBtn">
                            <img src="bilde.php" alt=""> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                        </p>
                        <div class="profile-dropdown" id="profileDropdown">
                            <p class="dropdown-option"><a href="PHPFiles/logout.php">Izlogoties</a></p>
                            <p class="dropdown-option"><a href="profils.php">Profils</a></p>
                            <p class="dropdown-option"><a href="pazinojumi.php">Paziņojumi</a></p>
                            
                            <?php if ($_SESSION["userType"] === "klients"): ?>
                                <p class="dropdown-option"><a href="IzveidotCV.php">Uztaisīt CV</a></p>
                            <?php elseif ($_SESSION["userType"] === "uznemums"): ?>
                                <p class="dropdown-option"><a href="IzveidotVakanci.php">Uztaisīt vakanci</a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="login-container">
                        <p class="login-btn" id="openLoginDropdown"><i class="fa-solid fa-right-to-bracket"></i> Ienākt</p>
                        <div class="login-dropdown" id="loginDropdown">
                            <p class="dropdown-option" data-user-type="klients">Klients</p>
                            <p class="dropdown-option" data-user-type="uznemums">Uzņēmums</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

<div class="accountprofile-container">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="profile-content">
            <div class="profile-image-container">
                <label for="profile-image" class="upload-image-label">
                    <img id="profile-image-preview" src="bilde.php" alt="Profile Image">
                    <input type="file" id="profile-image" name="profile_image" accept="image/*" style="display: none;">
                </label>
                <p>Noklikšķiniet lai nomainītu bildi</p>
            </div>

            <div class="profile-form">
                <h2>Mans Profils</h2>

                <?php if ($user_type === 'klients'): ?>
                    <label for="lietotajvards">Lietotājvārds:</label>
                    <input type="text" id="lietotajvards" name="lietotajvards" value="<?php echo htmlspecialchars($user['lietotajvards']); ?>" required>

                    <label for="vards">Vārds:</label>
                    <input type="text" id="vards" name="vards" value="<?php echo htmlspecialchars($user['vards']); ?>" required>

                    <label for="uzvards">Uzvārds:</label>
                    <input type="text" id="uzvards" name="uzvards" value="<?php echo htmlspecialchars($user['uzvards']); ?>" required>

                    <label for="epasts">E-pasts:</label>
                    <input type="email" id="epasts" name="epasts" value="<?php echo htmlspecialchars($user['epasts']); ?>" required>

                    <label for="parole">Parole (atstāj tukšu, ja nemaini):</label>
                    <input type="password" id="parole" name="parole" value="">

                <?php elseif ($user_type === 'uznemums'): ?>
                    <label for="uznemuma_nosaukums">Uzņēmuma nosaukums:</label>
                    <input type="text" id="uznemuma_nosaukums" name="uznemuma_nosaukums" value="<?php echo htmlspecialchars($user['uznemuma_nosaukums']); ?>" required>

                    <label for="registracijas_numurs">Reģistrācijas numurs:</label>
                    <input type="text" id="registracijas_numurs" name="registracijas_numurs" value="<?php echo htmlspecialchars($user['registracijas_numurs']); ?>" required>

                    <label for="uznemuma_epasts">E-pasts:</label>
                    <input type="email" id="uznemuma_epasts" name="uznemuma_epasts" value="<?php echo htmlspecialchars($user['uznemuma_epasts']); ?>" required>

                    <label for="uznemuma_TelNr">Telefona numurs:</label>
                    <input type="text" id="uznemuma_TelNr" name="uznemuma_TelNr" value="<?php echo htmlspecialchars($user['uznemuma_TelNr']); ?>" required>

                    <label for="PVN_numurs">PVN numurs:</label>
                    <input type="text" id="PVN_numurs" name="PVN_numurs" value="<?php echo htmlspecialchars($user['PVN_numurs']); ?>" required>

                    <label for="uznemuma_parole">Parole (atstāj tukšu, ja nemaini):</label>
                    <input type="password" id="uznemuma_parole" name="uznemuma_parole" value="">

                <?php endif; ?>

                <button type="submit">Saglabāt izmaiņas</button>

                <button type="button" class="delete-account-btn">Dzēst profilu</button>

                <div class="modal-overlay">
                    <div class="modal-delete-profile">
                        <h3>Apstiprini profila dzēšanu</h3>
                        <input type="text" id="confirm-username" placeholder="Lietotājvārds">
                        <input type="email" id="confirm-email" placeholder="E-pasts">
                        <input type="password" id="confirm-password" placeholder="Parole">
                        <button type="button" class="confirm-delete">Dzēst</button>
                        <button type="button" class="cancel-delete">Atcelt</button>
                    </div>
                </div>
                
            </div>
        </div>
    </form>
</div>

</body>
</html>
