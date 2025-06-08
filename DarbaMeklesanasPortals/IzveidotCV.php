<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['userType'] !== 'klients') {
    header('Location: index.php'); 
    exit;
}

if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true) {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['statuss']) && $_SESSION['statuss'] === 'deaktivizets') {
    header("Location: index.php");
    exit();
}

require 'PHPFiles/con_db.php';

// Iegūstiet lietotāja lietotājvārdu
$username = $_SESSION['username'];

// Vaicājums, lai iegūtu lietotāja CV 
$query = "SELECT * FROM DMPortals_CV WHERE lietotajvards = ?";
$stmt = $savienojums->prepare($query);
$stmt->bind_param("s", $username);

// Pārbaudiet, vai vaicājums tiek izpildīts veiksmīgi
if ($stmt->execute()) {
    $result = $stmt->get_result();

    // Saglabā rezultātu masīvā
    $cvs = [];
    while ($row = $result->fetch_assoc()) {
        $cvs[] = $row;
    }

    $stmt->close();
} else {
    echo "Kļūda, izpildot vaicājumu: " . $stmt->error;
    exit();
}

?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV izveidošana</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="Bildes/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="izveidotCV.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="autorizacija.js"></script>
    <script src="CVizveidosana.js"></script>
    <script src="gaismasRezims.js"></script>
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
                    <li><a href="index.php#Par">Par portālu</a></li>
                    <li><a href="index.php#features">Funkcionalitātes</a></li>
                    <li><a href="index.php#Darbi">Darba piedāvājumi</a></li>
                    <li><a href="index.php#Pieteikties">Pieteikties</a></li>
                </ul>
            </nav>

            <button id="themeToggle" class="theme-toggle">🌙</button>

            <div>
                <?php if (isset($_SESSION["username"])): ?>
                    <!-- Ielogojies lietotājs -->
                    <div class="profile-container">
                        <p class="profile-btn username-ellipsis" id="profileDropdownBtn">
                            <img src="bilde.php" alt=""><span class="username-ellipsis"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
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

            <button class="menu-toggle" aria-label="Toggle menu">
                &#9776;
            </button>

            <script>
                const menuToggle = document.querySelector(".menu-toggle");
                const navMenu = document.querySelector("nav ul");

                menuToggle.addEventListener("click", () => {
                    navMenu.classList.toggle("show");
                });
            </script>
        </div>
    </header>

    <main>
        <div class="container">
            <button id="cvButton" class="btn"><i class="fa-solid fa-plus"></i> Izveidot CV</button>
        </div>
    </main>

    <div class="augstums">
        <!-- Parāda izveidotos CV -->
        <div class="cv-container" id="cvGrid">
            <?php if (count($cvs) > 0): ?>
                <?php foreach ($cvs as $cv): ?>
                    <div class="cv-box" data-cv-id="<?php echo $cv['id']; ?>">
                        <img src="Bildes/CV.png" alt="CV Image" class="cv-image">
                        <p class="cv-language"><?php echo strtoupper(htmlspecialchars($cv['valoda'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="Pazinojums">Nav izveidots neviens CV!</p>
            <?php endif; ?>
        </div>

        <!-- Modālais logs priekš valodas izvēles -->
        <div id="languageModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2>Izvēlieties valodu</h2>
                <select id="languageSelect">
                    <option value="lv">Latviešu</option>
                    <option value="en">English</option>
                    <option value="ru">Русский</option>
                </select>
                <button id="confirmLanguage" class="btn">Apstiprināt</button>
            </div>
        </div>

            <!-- CV izveideidošana -->
        <div id="cvModal" class="modal">
            <div class="modal-content scroll-container">
                <h2>Create Your CV</h2>
                <div id="cvFields">
                    <!-- Personiskā informācija -->
                    <label id="nameLabel" for="name">Name:</label>
                    <input type="text" id="name" placeholder="Enter your name">

                    <label id="emailLabel" for="email">Email:</label>
                    <input type="email" id="email" placeholder="Enter your email">

                    <label id="phoneLabel" for="phone">Phone:</label>
                    <input type="tel" id="phone" placeholder="Enter your phone number">

                    <label id="addressLabel" for="address">Address:</label>
                    <input type="text" id="address" placeholder="Enter your address">

                    <label id="dobLabel" for="dob">Date of Birth:</label>
                    <input type="date" id="dob">

                    <!-- Izglītība -->
                    <label id="educationLabel" for="education">Education:</label>
                    <textarea id="education" placeholder="Enter your educational background"></textarea>

                    <!-- Darba pieredze -->
                    <label id="workExperienceLabel" for="workExperience">Work Experience:</label>
                    <textarea id="workExperience" placeholder="Enter your work experience"></textarea>

                    <!-- Prasmes -->
                    <label id="skillsLabel" for="skills">Skills:</label>
                    <textarea id="skills" placeholder="Enter your skills"></textarea>

                    <!-- Valodas -->
                    <label id="languagesLabel" for="languages">Languages Spoken:</label>
                    <textarea id="languages" placeholder="Enter the languages you speak"></textarea>

                    <label id="additionalInfoLabel" for="additionalInfo">Additional Information:</label>
                    <textarea id="additionalInfo" placeholder="Enter any other information"></textarea>

                    <input type="hidden" id="username" value="<?php echo $_SESSION['username']; ?>">

                    <input type="hidden" id="selectedLanguage" name="language">

                    <button id="saveCV" class="btn" disabled>Save CV</button>
                </div>

                <span id="closeCVModal" class="close">×</span>
                
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
