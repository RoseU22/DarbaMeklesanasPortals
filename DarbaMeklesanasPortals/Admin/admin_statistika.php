<?php
require '../PHPFiles/con_db.php';

$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $dates[] = date("Y-m-d", strtotime("-$i days"));
}
$labels = array_map(fn($d) => date("j.n", strtotime($d)), $dates);
$klienti = array_fill(0, 7, 0);
$uznemumi = array_fill(0, 7, 0);

// Klienti
$stmt = $savienojums->prepare("SELECT DATE(laiks) as reg_date, COUNT(*) as count FROM DMPortals WHERE laiks >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY reg_date");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $index = array_search($row['reg_date'], $dates);
    if ($index !== false) {
        $klienti[$index] = (int)$row['count'];
    }
}

// Uzņēmumi
$stmt = $savienojums->prepare("SELECT DATE(laiks) as reg_date, COUNT(*) as count FROM DMPortals_Uznemums WHERE laiks >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY reg_date");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $index = array_search($row['reg_date'], $dates);
    if ($index !== false) {
        $uznemumi[$index] = (int)$row['count'];
    }
}

echo json_encode([
    "labels" => $labels,
    "klienti" => $klienti,
    "uznemumi" => $uznemumi
]);
