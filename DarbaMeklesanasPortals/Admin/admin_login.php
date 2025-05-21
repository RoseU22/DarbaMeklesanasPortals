<?php
session_start();
require '../PHPFiles/con_db.php';

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

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $savienojums->prepare("SELECT * FROM DMPortals WHERE lietotajvards = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['admin_parole'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['lietotajvards'];
            header("Location: admin_panelis.php");
            exit();
        } else {
            $message = "Nepareis lietotājvārds vai parole.";
        }
    } else {
        $message = "Nepareis lietotājvārds vai parole.";
    }
}
?>


<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Admina Paneļa Autorizācija</title>
    <link rel="stylesheet" href="admin_login.css">
    <link rel="shortcut icon" href="../Bildes/Favicon.png" type="image/x-icon">

    <script src="../BurbuluAnimacija.js"></script>

</head>

<body>

  <div class="burbulu-background"></div>

  <div class="login-box">
    <a href="../index.php">
        <span class="closeModalBtn">&times;</span>
    </a>
    <h2>Admina Pieteikšanās</h2>
    <form method="POST">
      <label for="username">Lietotājvārds:</label>
      <input type="text" name="username" id="username" required>

      <label for="password">Parole:</label>
      <input type="password" name="password" id="password" required>

      <input type="submit" value="Pieteikties">
      <script>if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );}
      </script>
    </form>

    <?php if ($message): ?>
      <div class="error-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
  </div>

</body>

</html>
