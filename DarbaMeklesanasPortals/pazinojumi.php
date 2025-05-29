<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['userType'])) {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true) {
    header("Location: index.php");
    exit();
}

require 'PHPFiles/con_db.php';

$userType = $_SESSION['userType'];
$username = $_SESSION['username'];

$notifications = [];

if ($userType === 'uznemums') {
    $sql = "SELECT p.pazinojumi_id, p.klients_id, p.statuss, p.cv_id, k.lietotajvards, k.profila_bilde, v.vakances_nosaukums
            FROM DMPortals_Pazinojumi p
            JOIN DMPortals_Vakances v ON p.vakance_id = v.vakancesID
            JOIN DMPortals k ON p.klients_id = k.lietotajsID
            WHERE v.uznemuma_nosaukums = ? AND p.uznemums_izdzesa = 0
            ORDER BY p.pazinojumi_id DESC";
    $stmt = $savienojums->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
} elseif ($userType === 'klients') {
    $sql = "SELECT 
                p.pazinojumi_id,
                v.vakances_nosaukums,
                u.uznemuma_nosaukums,
                u.profila_bilde,
                u.uznemumsID,
                p.statuss
            FROM DMPortals_Pazinojumi p
            JOIN DMPortals_Vakances v ON p.vakance_id = v.vakancesID
            JOIN DMPortals_Uznemums u ON v.uznemuma_nosaukums = u.uznemuma_nosaukums
            WHERE p.klients_izdzesa = 0 AND p.klients_id = (
                SELECT lietotajsID FROM DMPortals WHERE lietotajvards = ?
            )
            ORDER BY p.pazinojumi_id DESC";
    $stmt = $savienojums->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);

    // Dabūd vakances kuras tika izveidotas šodien
    $sqlNewVacancies = "
        SELECT 
            v.vakancesID,
            v.vakances_nosaukums,
            u.uznemuma_nosaukums,
            u.profila_bilde,
            u.uznemumsID,
            v.izveidots_laiks
        FROM DMPortals_Vakances v
        JOIN DMPortals_Uznemums u ON v.uznemuma_nosaukums = u.uznemuma_nosaukums
        WHERE v.izveidots_laiks >= NOW() - INTERVAL 1 DAY
        AND v.vakancesID NOT IN (
            SELECT vakance_id FROM DMPortals_Pazinojumi 
            WHERE klients_id = (SELECT lietotajsID FROM DMPortals WHERE lietotajvards = ?)
        )
    ";
    $stmtNew = $savienojums->prepare($sqlNewVacancies);
    $stmtNew->bind_param("s", $username);
    $stmtNew->execute();
    $resultNew = $stmtNew->get_result();
    $newVacancies = $resultNew->fetch_all(MYSQLI_ASSOC);

    foreach ($newVacancies as $new) {
        $notifications[] = array_merge($new, ['jauna_vakance' => true]);
    }
}

?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Paziņojumi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="pazinojumi.css">
    <link rel="stylesheet" href="izveidotCV.css">
    <link rel="shortcut icon" href="Bildes/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="autorizacija.js"></script>
    <script src="apskatitCV.js"></script>
    <script src="gaismasRezims.js"></script>
    <script src="pazinojumu_filtrs.js"></script>
    <script src="iestatijumi.js"></script>
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

            <button id="themeToggle" class="theme-toggle">🌙</button>

            <div>
                <?php if (isset($_SESSION["username"])): ?>
                    <div class="profile-container">
                        <p class="profile-btn" id="profileDropdownBtn">
                            <img src="bilde.php" alt=""> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                        </p>
                        <div class="profile-dropdown" id="profileDropdown">
                            <p class="dropdown-option"><a href="PHPFiles/logout.php">Izlogoties</a></p>
                            <p class="dropdown-option"><a href="profils.php">Profils</a></p>

                            <?php if (!empty($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true): ?>
                                <p id="admin" class="dropdown-option"><a href="Admin/admin_panelis.php">Admin panelis</a></p>
                            <?php else: ?>
                                <p class="dropdown-option"><a href="pazinojumi.php">Paziņojumi</a></p>

                                <?php if ($_SESSION["userType"] === "klients" && $_SESSION["statuss"] !== "deaktivizets"): ?>
                                    <p class="dropdown-option"><a href="IzveidotCV.php">Uztaisīt CV</a></p>
                                <?php elseif ($_SESSION["userType"] === "uznemums" && $_SESSION["statuss"] !== "deaktivizets"): ?>
                                    <p class="dropdown-option"><a href="IzveidotVakanci.php">Uztaisīt vakanci</a></p>
                                <?php endif; ?>

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

    <h1 class="notification-header">Paziņojumi</h1>

    <?php if ($_SESSION["userType"] === "klients" && $_SESSION["statuss"] !== "deaktivizets"): ?>
        <div class="notification-filters">
            <button class="filter-btn active" data-filter="all">Visi</button>
            <button class="filter-btn" data-filter="new-vacancy">Jaunas vakances</button>
            <button class="filter-btn" data-filter="normal">Pieteikumi</button>
        </div>
    <?php endif; ?>

    <div class="notification-container">
        <?php if (isset($_SESSION['statuss']) && $_SESSION['statuss'] === 'deaktivizets'): ?>
            <p class="disabled-notifications">Jūsu konts tika deaktivizēts</p>
        <?php else: ?>
            <?php if (empty($notifications)): ?>
                <p class="no-notifications">Nav paziņojuma</p>
            <?php else: ?>
                <p class="no-notifications disabled-message">Šī iespēja ir atspējota</p>
                <?php foreach ($notifications as $note): ?>
                    <?php
                        $isNewVacancy = isset($note['jauna_vakance']);
                        $isUznemums = ($_SESSION['userType'] ?? '') === 'uznemums';
                    ?>
                    <div class="notification <?php echo $isNewVacancy ? 'new-vacancy' : 'normal'; ?>" data-type="<?php echo $isNewVacancy ? 'new-vacancy' : 'normal'; ?>">
                        <div class="info">
                            <?php if ($isUznemums): ?>
                                <img src="bilde.php?id=<?php echo $note['klients_id']; ?>&type=klients" alt="Klienta bilde">
                                <div class="nosaukumuSakartojums">
                                    <strong><?php echo htmlspecialchars($note['lietotajvards']); ?></strong>
                                    <span><?php echo htmlspecialchars($note['vakances_nosaukums']); ?></span>
                                </div>
                            <?php else: ?>
                                <img src="bilde.php?id=<?php echo htmlspecialchars($note['uznemumsID']); ?>&type=uznemums" alt="Uzņēmuma bilde">
                                <div class="nosaukumuSakartojums">
                                    <strong><?php echo htmlspecialchars($note['uznemuma_nosaukums']); ?></strong>
                                    <span><?php echo htmlspecialchars($note['vakances_nosaukums']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($isUznemums): ?>
                            <input type="hidden" name="pazinojumi_id" value="<?php echo $note['pazinojumi_id']; ?>">

                            <?php if ($note['statuss'] !== 'Akceptēts'): ?>
                                <button class="accept-btn" data-paz-id="<?php echo $note['pazinojumi_id']; ?>" title="Akceptēt pieprasījumu">✅</button>
                                <button class="delete-btn" data-paz-id="<?php echo $note['pazinojumi_id']; ?>" title="Dzēst paziņojumu">🗑️</button>
                                <button class="apskatit-btn" data-cv-id="<?php echo $note['cv_id']; ?>">Apskatīt</button>                                
                            <?php else: ?>
                                <span class="status"><?php echo htmlspecialchars($note['statuss']); ?></span>
                                <button class="apskatit-btn" data-cv-id="<?php echo $note['cv_id']; ?>">Apskatīt</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if ($isNewVacancy): ?>
                                <span class="status">Ielikt pogu Apskatīt</span>
                            <?php else: ?>
                                <span class="sent-status">
                                    <span class="status"><?php echo htmlspecialchars($note['statuss'] ?? 'Aizsūtīts'); ?></span>
                                    <button class="delete-btn" data-paz-id="<?php echo $note['pazinojumi_id']; ?>" title="Dzēst paziņojumu">🗑️</button>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Modal for Akceptēt pieprasījumu -->
    <div id="acceptModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Nosūtīt ziņu klientam</h2>
            <form id="acceptForm">
                <input type="hidden" name="pazinojumi_id" id="modalPazinojumiID">
                <textarea name="zina" id="zina" placeholder="Ziņa klientam..." required></textarea>
                <button type="submit">Nosūtīt</button>
            </form>
        </div>
    </div>
    
    <!-- CV modālais logs -->
    <div id="cvModal" class="modal">
        <div class="modal-content">
            <div id="cvFields">
                <!-- Personiskā informācija -->
                <label id="nameLabel" for="name">Name:</label>
                <input type="text" id="name" placeholder="Enter your name" readonly>

                <label id="emailLabel" for="email">Email:</label>
                <input type="email" id="email" placeholder="Enter your email" readonly>

                <label id="phoneLabel" for="phone">Phone:</label>
                <input type="tel" id="phone" placeholder="Enter your phone number" readonly>

                <label id="addressLabel" for="address">Address:</label>
                <input type="text" id="address" placeholder="Enter your address" readonly>

                <label id="dobLabel" for="dob">Date of Birth:</label>
                <input type="date" id="dob" readonly>

                <!-- Izglītība -->
                <label id="educationLabel" for="education">Education:</label>
                <textarea id="education" placeholder="Enter your educational background" readonly></textarea>

                <!-- Darba pieredze -->
                <label id="workExperienceLabel" for="workExperience">Work Experience:</label>
                <textarea id="workExperience" placeholder="Enter your work experience" readonly></textarea>

                <!-- Prasmes -->
                <label id="skillsLabel" for="skills">Skills:</label>
                <textarea id="skills" placeholder="Enter your skills" readonly></textarea>

                <!-- Valodas -->
                <label id="languagesLabel" for="languages">Languages Spoken:</label>
                <textarea id="languages" placeholder="Enter the languages you speak" readonly></textarea>

                <label id="additionalInfoLabel" for="additionalInfo">Additional Information:</label>
                <textarea id="additionalInfo" placeholder="Enter any other information" readonly></textarea>

                <input type="hidden" id="username" value="<?php echo $_SESSION['username']; ?>" readonly>

                <input type="hidden" id="selectedLanguage" name="language" readonly>
            </div>

            <span id="closeCVModal" class="close">×</span>
                    
        </div>
    </div>

    <?php if ($_SESSION["userType"] === "klients" && $_SESSION["statuss"] !== "deaktivizets"): ?>
        <button id="settingsButton" class="settings-btn">
            <i class="fa fa-gear fa-spin" style="font-size:24px"></i>
        </button>

        <div id="settingsModal" class="settings-modal hidden">
            <h4>Iestatījumi</h4>
            <label>
                <input type="checkbox" id="toggleNewVacancies" checked>Rādīt jaunās vakances
            </label>
        </div>
    <?php endif; ?>

</body>
</html>
