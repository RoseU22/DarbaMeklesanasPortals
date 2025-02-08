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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="BurbuluAnimacija.js"></script>
    <script src="autorizacija.js"></script>

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

        <!--Modālais logs (Autorizācija)-->
        <div id="loginModal" class="modal">

            <div class="modal-content">

                <span class="close">&times;</span>
                <h2>Pieslēgties</h2>

                <form id="loginForm">
                    <input type="text" name="username" placeholder="Lietotājvārds" required>
                    <input type="password" name="password" placeholder="Parole" required>
                    <button type="submit" class="btn">Ienākt</button>
                    <p><a href="#">Aizmirsi paroli?</a></p>
                    <p>Nav konta? <a href="#">Reģistrēties</a></p>
                </form>

            </div>

        </div>

    </header>

    <footer>

        <div class="konteiners">
            <p>&copy; 2025 Darba Meklēšanas Portāls. Visas tiesības aizsargātas.</p>
        </div>

    </footer>

</body>
</html>