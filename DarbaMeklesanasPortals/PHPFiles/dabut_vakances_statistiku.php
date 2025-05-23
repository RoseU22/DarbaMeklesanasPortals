<?php
require 'con_db.php';

if (isset($_GET['vacancy_id'])) {
    $vacancyId = intval($_GET['vacancy_id']);

    // Sagatavo datumu pirms 7 dienām
    $startDate = (new DateTime())->modify('-6 days')->format('Y-m-d');

    $stmt = $savienojums->prepare("
        SELECT DATE(laiks) AS date, COUNT(*) AS count 
        FROM DMPortals_Pazinojumi 
        WHERE vakance_id = ? AND laiks >= ? 
        GROUP BY DATE(laiks)
    ");
    $stmt->bind_param("is", $vacancyId, $startDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $dailyCounts = [];
    while ($row = $result->fetch_assoc()) {
        $dailyCounts[$row['date']] = (int)$row['count'];
    }

    echo json_encode(['success' => true, 'dailyCounts' => $dailyCounts]);
} else {
    echo json_encode(['success' => false, 'error' => 'Trūkst vakances_id']);
}
?>
