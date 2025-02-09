<?php
    session_start(); 
?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darba Meklēšanas Portāls</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="Bildes/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="izveidotCV.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="autorizacija.js"></script>
    <script src="CVizveidosana.js"></script>
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
            <button id="cvButton" class="btn">Izveidot CV</button>
        </div>
    </main>

    <!-- Modal Window for Language Selection -->
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

    <footer>
        <div class="konteiners">
            <p>&copy; 2025 Darba Meklēšanas Portāls. Visas tiesības aizsargātas.</p>
        </div>
    </footer>
</body>
</html>
