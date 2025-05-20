<?php
require 'con_db.php';

if (isset($_GET['vacancy_id'])) {
    $vacancyId = intval($_GET['vacancy_id']);

    $stmt = $savienojums->prepare("SELECT COUNT(*) AS count FROM DMPortals_Pazinojumi WHERE vakance_id = ?");
    $stmt->bind_param("i", $vacancyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = 0;

    if ($row = $result->fetch_assoc()) {
        $count = $row['count'];
    }

    echo json_encode(['success' => true, 'count' => $count]);
} else {
    echo json_encode(['success' => false, 'error' => 'Trūkst vakances_id']);
}
?>
