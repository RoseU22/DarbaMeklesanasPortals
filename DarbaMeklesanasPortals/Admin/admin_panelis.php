<?php

session_start(); 

    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: admin_login.php');
        exit;
    }

    if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit;
    }

    if ($_SESSION['userType'] !== 'klients') {
        header('Location: ../index.php'); 
        exit;
    }

    if (!isset($_SESSION['loma']) || strtolower($_SESSION['loma']) !== 'administrators') {
        header('Location: ../index.php');
        exit;
    }
    
    $userType = $_SESSION["userType"];

    require '../PHPFiles/con_db.php';

    if (empty($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header("Location: admin_login.php");
    exit;
    }

    $result = $savienojums->query("SELECT COUNT(*) as count FROM DMPortals");
    $klientsCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

    $result = $savienojums->query("SELECT COUNT(*) as count FROM DMPortals_Uznemums");
    $uznemumsCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

    $result = $savienojums->query("SELECT COUNT(*) as count FROM DMPortals_Vakances");
    $vakancesCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

    $result = $savienojums->query("SELECT COUNT(*) as count FROM DMPortals_CV");
    $cvCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

    //Dabūd klientus
    $klientiData = [];
    $sql = "SELECT lietotajsID, statuss, lietotajvards FROM DMPortals WHERE loma = '' OR loma IS NULL";
    $result = $savienojums->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $klientiData[] = $row;
        }
    }

    $uznemumiData = [];
    $sql = "SELECT uznemumsID, uznemuma_nosaukums, statuss, apstiprinats FROM DMPortals_Uznemums";
    $result = $savienojums->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $uznemumiData[] = $row;
        }
    }

    $savienojums->close();

?>

<!DOCTYPE html>
<html lang="lv">

<head>

    <script>
        window.totalKlients = <?= json_encode($klientsCount) ?>;
        window.totalUznemums = <?= json_encode($uznemumsCount) ?>;
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../Bildes/Favicon.png" type="image/x-icon">
    <title>Admin Panelis</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../pazinojumi.css">
    <link rel="stylesheet" href="admin_panelis.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <script src="../autorizacija.js"></script>
    <script src="../gaismasRezims.js"></script>
    <script src="adminStatistika.js"></script>
    <script src="adminKlienti.js"></script>
    <script src="sekcijasMaina.js"></script>
    <script src="adminLog.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

    <header>
        <div class="konteiners">
            <div class="header-left">

                <a href="../index.php">

                    <img src="../Bildes/Favicon.png" alt="Logo" class="header-logo">
                    <h1>Darba Meklēšanas Portāls</h1>

                </a>
                
            </div>
            <nav>
                <ul>
                    <li><a href="#" data-target="statistics-container">Statistika</a></li>
                    <li><a href="#" data-target="klienti-section">Klienti</a></li>
                    <li><a href="#" data-target="uznemumi-section">Uzņēmumi</a></li>
                    <li><a href="#" data-target="adminlog-section">Žurnāls</a></li>
                </ul>
            </nav>

            <div class="header-controls">

                <button id="themeToggle" class="theme-toggle">🌙</button>

                <div>
                    <?php if (isset($_SESSION["username"])): ?>
                        <!-- Ielogojies lietotājs -->
                        <div class="profile-container">
                            <p class="profile-btn" id="profileDropdownBtn">
                                <img src="../bilde.php" alt=""> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                            </p>
                            <div class="profile-dropdown" id="profileDropdown">
                                <p class="dropdown-option"><a href="../PHPFiles/logout.php">Izlogoties</a></p>
                                <p class="dropdown-option"><a href="../profils.php">Profils</a></p>

                                <?php if (!empty($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true): ?>
                                    <p id="admin" class="dropdown-option"><a href="admin_panelis.php">Admin panelis</a></p>
                                <?php else: ?>
                                    <p class="dropdown-option"><a href="pazinojumi.php">Paziņojumi</a></p>

                                    <?php if ($_SESSION["userType"] === "klients"): ?>
                                        <p class="dropdown-option"><a href="../IzveidotCV.php">Uztaisīt CV</a></p>
                                    <?php elseif ($_SESSION["userType"] === "uznemums"): ?>
                                        <p class="dropdown-option"><a href="../IzveidotVakanci.php">Uztaisīt vakanci</a></p>
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
        </div>
    </header>


    <div id="statistics-container" class="section">
        <!--Statistika-->
        <div class="statistics-container">
            <div class="chart-wrapper">
                <canvas id="userChart"></canvas>
            </div>
            <div class="stats-wrapper">
                <div class="stat-item">
                    <h3>Klientu lietotāji:</h3>
                    <p><?= $klientsCount ?></p>
                </div>
                <div class="stat-item">
                    <h3>Uzņemumu lietotāji:</h3>
                    <p><?= $uznemumsCount ?></p>
                </div>
                <div class="stat-item">
                    <h3>Izveidotās vakances:</h3>
                    <p><?= $vakancesCount ?></p>
                </div>
                <div class="stat-item">
                    <h3>Izveidotie CV:</h3>
                    <p><?= $cvCount ?></p>
                </div>
            </div>
        </div>
    </div>
    
    
    <div id="klienti-section" class="section" style="display:none;">
        <div class="notification-container">
            <?php if (!empty($klientiData)): ?>
                <?php foreach ($klientiData as $klients): ?>
                    <div class="notification">
                        <div class="info">
                            <img src="../bilde.php?id=<?= htmlspecialchars($klients['lietotajsID']) ?>&type=klients" alt="Klienta bilde">
                            <div class="text">
                                <p><?= htmlspecialchars($klients['lietotajvards']) ?></p>
                            </div>
                        </div>
                        <div class="sent-status">
                            <?php if (empty($klients['statuss'])): ?>
                                <!-- Deaktivizēt -->
                                <form action="../PHPFiles/deaktivizet_lietotaju.php" method="POST">
                                    <input type="hidden" name="lietotajsID" value="<?= $klients['lietotajsID'] ?>">
                                    <button type="submit" title="Deaktivēt klientu" class="delete-btn">
                                        🔒
                                    </button>
                                </form>
                            <?php elseif ($klients['statuss'] === 'deaktivizets'): ?>
                                <!-- Aktivizēt -->
                                <form action="../PHPFiles/aktivizet_lietotaju.php" method="POST">
                                    <input type="hidden" name="lietotajsID" value="<?= $klients['lietotajsID'] ?>">
                                    <button type="submit" title="Aktivizēt klientu" class="activate-btn">
                                        🔓
                                    </button>
                                </form>
                            <?php endif; ?>

                            <!-- Uztaisīt par Admin -->
                            <button class="open-password-modal-btn" data-userid="<?= $klients['lietotajsID'] ?>">
                                <i class="fa-solid fa-user"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-notifications">Nav reģistrētu klientu</div>
            <?php endif; ?>
        </div>
    </div>

    <div id="passwordModal" class="password-modal">
        <div class="password-modal-content">
            <span id="closePasswordModal" class="close">&times;</span>
            <h2>Izveido paroli</h2>
            <form id="passwordForm" method="POST" action="../PHPFiles/izveidot_admin.php">
            <input type="hidden" name="userId" id="userId" value="">
            <label for="password">Parole:</label>
            <input type="password" id="password" name="password" required />
            <button type="submit" class="accept-btn">Saglabāt</button>
            </form>
        </div>
    </div>


    <div id="uznemumi-section" class="section" style="display:none;">
        <!--Uzņēmumi-->
        <div class="notification-container">
            <?php if (!empty($uznemumiData)): ?>
                <?php foreach ($uznemumiData as $uznemums): ?>
                    <div class="notification">
                        <div class="info">
                            <img src="../bilde.php?id=<?= htmlspecialchars($uznemums['uznemumsID']) ?>&type=uznemums" alt="Uzņēmuma bilde">
                            <div class="text">
                                <p><?= htmlspecialchars($uznemums['uznemuma_nosaukums']) ?></p>
                            </div>
                        </div>
                        <div class="sent-status">
                            <?php if (empty($uznemums['apstiprinats'])): ?>
                                <!-- Apstiprināt lietotāju -->
                                <form action="../PHPFiles/apstiprinat_lietotaju.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="uznemumsID" value="<?= $uznemums['uznemumsID'] ?>">
                                    <button type="submit" title="Apstiprināt uzņēmumu" class="approve-btn">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>

                                <!-- Apskatīt dokumentu poga -->
                                <a href="lejupieladet_dokumentu.php?uznemumsID=<?= urlencode($uznemums['uznemumsID']) ?>" class="download-btn" target="_blank" title="Apskatīt dokumentu">
                                    Apskatīt
                                </a>
                            <?php else: ?>
                                <?php if ($uznemums['statuss'] !== 'deaktivizets'): ?>
                                    <!-- Deaktivēt -->
                                    <form action="../PHPFiles/deaktivizet_lietotaju.php" method="POST">
                                        <input type="hidden" name="uznemumsID" value="<?= $uznemums['uznemumsID'] ?>">
                                        <input type="hidden" name="type" value="uznemums">
                                        <button type="submit" title="Deaktivēt uzņēmumu" class="delete-btn">
                                            🔒
                                        </button>
                                    </form>
                                <?php elseif ($uznemums['statuss'] === 'deaktivizets'): ?>
                                    <!-- Aktivizēt -->
                                    <form action="../PHPFiles/aktivizet_lietotaju.php" method="POST">
                                        <input type="hidden" name="uznemumsID" value="<?= $uznemums['uznemumsID'] ?>">
                                        <input type="hidden" name="type" value="uznemums">
                                        <button type="submit" title="Aktivizēt uzņēmumu" class="activate-btn">
                                            🔓
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-notifications">Nav reģistrētu uzņēmumu</div>
            <?php endif; ?>
        </div>
    </div>

    <div id="adminlog-section" class="section" style="display:none;">
        <div class="admin-log-container">
            <h2>Administratora darbības žurnāls</h2>
            <div id="admin-log-content">
                <!-- AJAX ielādētais saturs -->
            </div>
        </div>
    </div>


</body>

</html>