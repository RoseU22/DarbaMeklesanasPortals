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

    // Fetch counts from database
    // Klients count
    $result = $savienojums->query("SELECT COUNT(*) as count FROM DMPortals");
    $klientsCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

    // Uznemums count
    $result = $savienojums->query("SELECT COUNT(*) as count FROM DMPortals_Uznemums");
    $uznemumsCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

    // Vakances count
    $result = $savienojums->query("SELECT COUNT(*) as count FROM DMPortals_Vakances");
    $vakancesCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

    // CV count
    $result = $savienojums->query("SELECT COUNT(*) as count FROM DMPortals_CV");
    $cvCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

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
    <link rel="stylesheet" href="admin_panelis.css">
    
    <script src="../autorizacija.js"></script>
    <script src="../gaismasRezims.js"></script>
    <script src="adminStatistika.js"></script>
    <script src="sekcijasMaina.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

    <header>
        <div class="konteiners">
            <div class="header-left">

                <a href="index.php">

                    <img src="../Bildes/Favicon.png" alt="Logo" class="header-logo">
                    <h1>Darba Meklēšanas Portāls</h1>

                </a>
                
            </div>
            <nav>
                <ul>
                    <li><a href="#" data-target="statistics-container">Statistika</a></li>
                    <li><a href="#" data-target="klienti-section">Klienti</a></li>
                    <li><a href="#" data-target="uznemumi-section">Uzņēmumi</a></li>
                </ul>
            </nav>

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
        <!--Klienti-->
    </div>

    <div id="uznemumi-section" class="section" style="display:none;">
        <!--Uzņēmumi-->
    </div>
    
</body>

</html>