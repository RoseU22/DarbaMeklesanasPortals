<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

require 'PHPFiles/con_db.php';

$username = $_SESSION['username'];


echo "<script>console.log('Session username: " . $username . "');</script>";


$query = "SELECT * FROM DMPortals_Vakances WHERE uznemuma_nosaukums = ?";
$stmt = $savienojums->prepare($query);

if (!$stmt) {
    die("Error preparing query: " . $savienojums->error);
}

$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    $result = $stmt->get_result();

    $vacancies = [];
    while ($row = $result->fetch_assoc()) {
        $vacancies[] = $row;
    }

    echo "<script>console.log('Found " . count($vacancies) . " vacancies');</script>";
    foreach ($vacancies as $vakance) {
        echo "<script>console.log(" . json_encode($vakance) . ");</script>";
    }

    $stmt->close();
} else {
    echo "Error executing query: " . $stmt->error;
    exit();
}


?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darba Meklēšanas Portāls</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="Bildes/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="izveidotVakanci.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="autorizacija.js"></script>
    <script src="VakancesIzveidosana.js"></script>
</head>

<body>

    <input type="hidden" id="loginState" value="<?php echo isset($_SESSION["username"]) ? 'true' : 'false'; ?>">

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
                    <!-- Ielogojies lietotājs -->
                    <div class="profile-container">
                        <p class="profile-btn" id="profileDropdownBtn">
                            <i class="fa-solid fa-person"></i> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                        </p>
                        <div class="profile-dropdown" id="profileDropdown">
                            <p class="dropdown-option"><a href="PHPFiles/logout.php">Izlogoties</a></p>
                            <p class="dropdown-option"><a href="profile.php">Profils</a></p>
                            
                            <?php if ($_SESSION["userType"] === "klients"): ?>
                                <p class="dropdown-option"><a href="IzveidotCV.php">Uztaisīt CV</a></p>
                            <?php elseif ($_SESSION["userType"] === "uznemums"): ?>
                                <p class="dropdown-option"><a href="IzveidotVakanci.php">Uztaisīt vakanci</a></p>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Izrakstījies lietotājs -->
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

    <main>
        <div class="container">
            <button id="vacancyButton" class="btn"><i class="fa-solid fa-plus"></i> Izveidot Vakanci</button>
        </div>
    </main>

    <div class="augstums">

        <!-- Parāda izveidotās vakances -->
        <div class="vacancy-container" id="vacancyGrid">
            <?php if (count($vacancies) > 0): ?>
                <?php foreach ($vacancies as $vacancy): ?>
                    <div class="vacancy-box" data-vacancy-id="<?php echo $vacancy['vakancesID']; ?>">
                        <p class="vacancy-title"><?php echo htmlspecialchars($vacancy['vakances_nosaukums']); ?></p>
                        <img src="Bildes/Vakance.png" alt="Vakance Image" class="vakance-image">
                        <p class="vacancy-location"><?php echo htmlspecialchars($vacancy['atrasanas_vieta']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="Pazinojums">Nav izveidotas nevienas vakances!</p>
            <?php endif; ?>
        </div>

        <!-- Vakances modāls -->
        <div id="vacancyModal" class="modal">
            <div class="modal-content">
                <span id="closeVacancyModal" class="close">&times;</span>
                <h2>Izveidot vakanci</h2>
                <label for="vacancyName">Vacancy Title:</label>
                <input type="text" id="vacancyName" placeholder="Enter vacancy title">

                <label for="vacancyDescription">Description:</label>
                <textarea id="vacancyDescription" placeholder="Enter vacancy description"></textarea>

                <label for="vacancyLocation">Location:</label>
                <input type="text" id="vacancyLocation" placeholder="Enter location">

                <label for="vacancySkills">Required Skills:</label>
                <textarea id="vacancySkills" placeholder="Enter required skills"></textarea>

                <label for="vacancySalary">Salary:</label>
                <input type="number" id="vacancySalary" step="0.01" placeholder="Enter salary">

                <button id="saveVacancy" disabled>Save Vacancy</button>
            </div>
        </div>
    </div>

    <footer>
        <div class="konteiners">
            <p>&copy; 2025 Darba Meklēšanas Portāls. Visas tiesības aizsargātas.</p>
        </div>
    </footer>
</body>
</html>
