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

        <!--Modālais logs (Autorizācija)-->
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Pieslēgties</h2>

                <form id="loginForm">
                    <div id="clientFields">
                        <input type="text" name="username" placeholder="Lietotājvārds" required>
                        <input type="password" name="password" placeholder="Parole" required>
                    </div>

                    <div id="companyFields" style="display: none;">
                        <input type="text" name="company_name" placeholder="Uzņēmuma nosaukums" required>
                        <input type="text" name="registration_number" placeholder="Reģistrācijas numurs" required>
                        <input type="email" name="company_email" placeholder="Uzņēmuma e-pasts" required>
                        <input type="text" name="phone_number" placeholder="Tālrunis" required>
                        <input type="text" name="vat_number" placeholder="PVN numurs" required>
                    </div>
                    
                    <button type="submit" class="btn">Ienākt</button>
                    <p><a href="#">Aizmirsi paroli?</a></p>
                    <p>Nav konta? <a href="#" class="register">Reģistrēties</a></p>
                </form>
            </div>
        </div>

        <!-- Modālais logs (Reģistrācija) -->
        <div id="registerModal" class="modal">

            <div class="modal-content">
                
                <span class="close">&times;</span>
                <h2>Reģistrēties</h2>

                <form id="registerForm">
                    <input type="text" name="lietotajvards" placeholder="Lietotājvārds" required>
                    <input type="text" name="vards" placeholder="Vārds" required>
                    <input type="text" name="uzvards" placeholder="Uzvārds" required>
                    <input type="password" name="parole" placeholder="Parole" required>
                    <input type="email" name="epasts" placeholder="E-pasts" required>
                    <button type="submit" class="btn">Reģistrēties</button>
                </form>

            </div>

        </div>


    </header>

    <section id="sakums">

        <div class="konteiners">
            <h2>Atrodi savu ideālo darbu jau šodien!</h2>
            <p>Ērta un efektīva vide darba meklētājiem un darba devējiem.</p>

            <?php if (!isset($_SESSION["username"])): ?>
                <p id="openLogin2" class="btn">Izveidot CV</p>
            <?php else: ?>
                <a href="IzveidotCV.php" class="btn">Izveidot CV</a>
            <?php endif; ?>

        </div>
        
    </section>


    <div class="burbulu-background"></div>
    

    <section id="features">

        <div class="konteiners">

            <h2>Galvenās funkcionalitātes</h2>
            <ul>
                <li>Vakances pievienošana un pārvaldība</li>
                <li>Pieteikšanās uz darba piedāvājumiem</li>
                <li>Automātiski ieteikumi atbilstoši CV</li>
                <li>CV veidošana tiešsaistē</li>
            </ul>

        </div>

    </section>
    

    <section id="Darbi">

        <div class="konteiners">
            <h2>Aktuālie darba piedāvājumi</h2>
            <p>Atrodi sev piemērotu vakanci no mūsu plašā darba devēju tīkla.</p>
        </div>

    </section>
    

    <section id="Pieteikties">

        <div class="konteiners">
            <h2>Pieteikties darbam</h2>
            <p>Aizpildi savu CV un sūti pieteikumus ar pāris klikšķiem!</p>
            <a href="#" class="btn">Sākt tagad</a>
        </div>

    </section>
    

    <footer>

        <div class="konteiners">
            <p>&copy; 2025 Darba Meklēšanas Portāls. Visas tiesības aizsargātas.</p>
        </div>

    </footer>

</body>

</html>
