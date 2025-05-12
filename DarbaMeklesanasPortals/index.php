<?php

    session_start(); 

    if (!isset($_SESSION["userType"])) {
        $_SESSION["userType"] = "klients";
    }
    
    $userType = $_SESSION["userType"];

    require 'PHPFiles/con_db.php';

    $vakances = [];
    $vakances_query = "SELECT * FROM DMPortals_Vakances ORDER BY vakancesID DESC";
    $result = $savienojums->query($vakances_query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $vakances[] = $row;
        }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="BurbuluAnimacija.js"></script>
    <script src="autorizacija.js"></script>
    <script src="Pieteiksanas.js"></script>

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

        <!--Modālais logs (Autorizācija)-->
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Pieslēgties</h2>

                <form id="loginForm" method="POST" action="PHPFiles/login.php">
                    <input type="hidden" name="userType" id="loginUserType" value="<?php echo $userType; ?>">

                    <div id="LoginClientFields" <?php echo $userType === "klients" ? '' : 'style="display:none;"'; ?>>
                        <input type="text" name="username" placeholder="Lietotājvārds" required>
                        <input type="password" name="password" placeholder="Parole" required>
                    </div>

                    <div id="LoginCompanyFields" <?php echo $userType === "uznemums" ? '' : 'style="display:none;"'; ?>>
                        <input type="text" name="company_name" placeholder="Uzņēmuma nosaukums" required>
                        <!-- <input type="email" name="company_email" placeholder="Uzņēmuma e-pasts" required> -->
                        <input type="password" name="company_password" placeholder="Parole" required>
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

                <form id="registerForm" method="POST" action="PHPFiles/register.php">
                    <input type="hidden" name="userType" id="registerUserType" value="<?php echo $userType; ?>">

                    <div id="clientFields" <?php echo $userType === "klients" ? '' : 'style="display:none;"'; ?>>
                        <input type="text" name="lietotajvards" placeholder="Lietotājvārds" required>
                        <input type="text" name="vards" placeholder="Vārds" required>
                        <input type="text" name="uzvards" placeholder="Uzvārds" required>
                        <input type="password" name="parole" placeholder="Parole" required>
                        <input type="email" name="epasts" placeholder="E-pasts" required>
                    </div>

                    <div id="companyFields" <?php echo $userType === "uznemums" ? '' : 'style="display:none;"'; ?>>
                        <input type="text" name="companyName" placeholder="Uzņēmuma nosaukums" required>
                        <input type="text" name="regNumber" placeholder="Reģistrācijas numurs" required>
                        <input type="email" name="companyEmail" placeholder="Uzņēmuma e-pasts" required>
                        <input type="text" name="phone" placeholder="Telefona numurs" required>
                        <input type="text" name="vatNumber" placeholder="PVN numurs" required>
                        <input type="password" name="companyPassword" placeholder="Parole" required>
                    </div>

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

            <div class="vakances-konteiners">
                <?php foreach ($vakances as $vakance): ?>
                    <div class="vacancy-box" data-vacancy-id="<?php echo $vakance['vakancesID']; ?>">
                        <p class="vacancy-title"><?php echo htmlspecialchars($vakance['vakances_nosaukums']); ?></p>
                        <img src="Bildes/Vakance.png" alt="Vakance Image" class="vakance-image">
                        <p class="vacancy-location"><?php echo htmlspecialchars($vakance['atrasanas_vieta']); ?></p>
                        <button class="openModalBtn" 
                                data-id="<?php echo $vakance['vakancesID']; ?>"
                                data-title="<?php echo htmlspecialchars($vakance['vakances_nosaukums']); ?>"
                                data-description="<?php echo htmlspecialchars($vakance['vakances_apraksts']); ?>"
                                data-location="<?php echo htmlspecialchars($vakance['atrasanas_vieta']); ?>"
                                data-skills="<?php echo htmlspecialchars($vakance['nepieciesamas_prasmes']); ?>"
                                data-salary="<?php echo htmlspecialchars($vakance['maksa']); ?>">
                            Apskatīt
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Pieteikšanās modālais logs -->
    <div id="vacancyApplyModal" class="PieteiksanasModal">
        <div class="modal-content">
            <span class="closeModalBtn">&times;</span>
            <div class="modal-left">
                <img src="Bildes/Vakance.png" alt="Vakance">
            </div>
            <div class="modal-right">
                <h2 id="modalVacancyTitle"></h2>
                <p><strong>Apraksts:</strong> <span id="modalVacancyDescription"></span></p>
                <p><strong>Atrašanās vieta:</strong> <span id="modalVacancyLocation"></span></p>
                <p><strong>Nepieciešamās prasmes:</strong> <span id="modalVacancySkills"></span></p>
                <p><strong>Alga:</strong> <span id="modalVacancySalary"></span></p>
                <?php if ($_SESSION["userType"] === "klients"): ?>
                    <button class="applyBtn" data-vacancy-id="<?php echo $vakance['vakancesID']; ?>">
                        Pieteikties
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- CV Izvēles modālais logs -->
    <div id="cvSelectModal" class="modal-CVselect">
        <div class="modal-content-CVselect">
            <span class="closeCvModalBtn">&times;</span>
            <h2>Pievienot CV</h2>
            <div class="cv-placeholder" id="cvUploadArea">
                <div class="cv-drop-box">Ievelciet CV</div>
            </div>
            <div id="availableCvs" style="margin-top: 1rem;"></div>
            <button id="submitApplicationBtn" disabled style="margin-top: 1rem;">Pieteikties</button>
        </div>
    </div>

    <footer>

        <div class="konteiners">
            <p>&copy; 2025 Darba Meklēšanas Portāls. Visas tiesības aizsargātas.</p>
        </div>

    </footer>

</body>

</html>
