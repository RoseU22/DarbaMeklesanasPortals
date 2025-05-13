<?php
session_start();
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'uznemums') {
    header("Location: index.php");
    exit();
}

require 'PHPFiles/con_db.php';

$companyName = $_SESSION['username']; // This holds uznemuma_nosaukums

// Fetch notifications (CV applications sent to this company's vacancies)
$sql = "SELECT p.pazinojumi_id, p.klients_id, k.lietotajvards, k.profila_bilde
        FROM DMPortals_Pazinojumi p
        JOIN DMPortals_Vakances v ON p.vakance_id = v.vakancesID
        JOIN DMPortals k ON p.klients_id = k.lietotajsID
        WHERE v.uznemuma_nosaukums = ?";
$stmt = $savienojums->prepare($sql);
$stmt->bind_param("s", $companyName);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Paziņojumi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="pazinojumi.css">
    <link rel="shortcut icon" href="Bildes/Favicon.png" type="image/x-icon">
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
                        <?php if ($_SESSION["userType"] === "klients"): ?>
                            <p class="dropdown-option"><a href="IzveidotCV.php">Uztaisīt CV</a></p>
                        <?php elseif ($_SESSION["userType"] === "uznemums"): ?>
                            <p class="dropdown-option"><a href="pazinojumi.php">Paziņojumi</a></p>
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
                    <img src="bilde.php?id=<?php echo $note['klients_id']; ?>&type=klients" alt="Klienta bilde">
                    <strong><?php echo htmlspecialchars($note['lietotajvards']); ?></strong>
                </div>
                <form action="apskatit_cv.php" method="GET">
                    <input type="hidden" name="pazinojumi_id" value="<?php echo $note['pazinojumi_id']; ?>">
                    <button type="submit">Apskatīt</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
