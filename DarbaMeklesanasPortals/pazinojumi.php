<?php
session_start();
if (!isset($_SESSION['userType'])) {
    header("Location: index.php");
    exit();
}

require 'PHPFiles/con_db.php';

$userType = $_SESSION['userType'];
$username = $_SESSION['username'];

$notifications = [];

if ($userType === 'uznemums') {
    $sql = "SELECT p.pazinojumi_id, p.klients_id, p.cv_id, k.lietotajvards, k.profila_bilde, v.vakances_nosaukums
            FROM DMPortals_Pazinojumi p
            JOIN DMPortals_Vakances v ON p.vakance_id = v.vakancesID
            JOIN DMPortals k ON p.klients_id = k.lietotajsID
            WHERE v.uznemuma_nosaukums = ?";
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
                u.uznemumsID
            FROM DMPortals_Pazinojumi p
            JOIN DMPortals_Vakances v ON p.vakance_id = v.vakancesID
            JOIN DMPortals_Uznemums u ON v.uznemuma_nosaukums = u.uznemuma_nosaukums
            WHERE p.klients_id = (
                SELECT lietotajsID FROM DMPortals WHERE lietotajvards = ?
            )";
    
    $stmt = $savienojums->prepare($sql);
    if (!$stmt) {
        die("SQL prepare error: " . $savienojums->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
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
    <script src="autorizacija.js"></script>
    <script src="apskatitCV.js"></script>
</head>
<body>

    <header>
        <div class="konteiners">
            <div class="header-left">
                <img src="Bildes/Favicon.png" alt="Logo" class="header-logo">
                <h1>Darba Meklēšanas Portāls</h1>
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

    <h1 class="notification-header">Paziņojumi</h1>

    <div class="notification-container">
        <?php if (empty($notifications)): ?>
            <p class="no-notifications">Nav paziņojuma</p>
        <?php else: ?>
            <?php foreach ($notifications as $note): ?>
                <div class="notification">
                    <div class="info">
                        <?php if ($userType === 'uznemums'): ?>
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

                    <?php if ($userType === 'uznemums'): ?>
                        <input type="hidden" name="pazinojumi_id" value="<?php echo $note['pazinojumi_id']; ?>">
                        <button class="delete-btn" data-paz-id="<?php echo $note['pazinojumi_id']; ?>" title="Dzēst paziņojumu">🗑️</button>
                        <button class="apskatit-btn" data-cv-id=<?php echo $note['cv_id']; ?>>Apskatīt</button>
                    <?php else: ?>
                        <span class="sent-status">Aizsūtīts</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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

</body>
</html>
