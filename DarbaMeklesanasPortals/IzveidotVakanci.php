<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

require 'PHPFiles/con_db.php';

$username = $_SESSION['username'];


$query = "SELECT * FROM DMPortals_Uznemums WHERE uznemuma_nosaukums = ?";
$stmt = $savienojums->prepare($query);
$stmt->bind_param("s", $username);


if ($stmt->execute()) {
    $result = $stmt->get_result();


    $vacancies = [];
    while ($row = $result->fetch_assoc()) {
        $vacancies[] = $row;
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
    <!-- <link rel="stylesheet" href="izveidotVakanci.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="autorizacija.js"></script>
    <script src="VakancesIzveidosana.js"></script>
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
                    <p class="login-btn">
                        <a href="PHPFiles/logout.php"><i class="fa-solid fa-person"></i><?php echo htmlspecialchars($_SESSION["username"]); ?></a>
                    </p>
                <?php else: ?>
                    <p class="login-btn" id="openLogin"><i class="fa-solid fa-right-to-bracket"></i> Ienākt</p>
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
                    <div class="vacancy-box" data-vacancy-id="<?php echo $vacancy['id']; ?>">
                        <p class="vacancy-title"><?php echo htmlspecialchars($vacancy['title']); ?></p>
                        <p class="vacancy-location"><?php echo htmlspecialchars($vacancy['location']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="Pazinojums">Nav izveidotas nevienas vakances!</p>
            <?php endif; ?>
        </div>

        <!-- Vakances modāls -->
        <div id="vacancyModal" class="modal">
            <div class="modal-content">
                <h2>Create Vacancy</h2>
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

                <button id="saveVacancy">Save Vacancy</button>
                <button id="closeVacancyModal">Close</button>
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
