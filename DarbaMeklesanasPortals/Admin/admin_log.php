<?php
require '../PHPFiles/con_db.php';

$logsPerPage = 5;
$page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
$offset = ($page - 1) * $logsPerPage;

// Total logs count
$totalResult = $savienojums->query("SELECT COUNT(*) as total FROM DMPortals_AdminLog");
$totalLogs = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalLogs / $logsPerPage);

// Fetch logs
$stmt = $savienojums->prepare("
    SELECT admin_id, admin_lietotajvards AS admin_username, apraksts AS action_text, laiks AS action_time
    FROM DMPortals_AdminLog
    ORDER BY laiks DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $logsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
$logs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (!empty($logs)): 
    foreach ($logs as $log): ?>
        <div class="admin-log-entry">
            <img src="../bilde.php?id=<?= htmlspecialchars($log['admin_id']) ?>&type=klients" alt="Admin bilde">
            <div class="log-details">
                <strong><?= htmlspecialchars($log['admin_username']) ?></strong>
                <p><?= htmlspecialchars($log['action_text']) ?></p>
                <small><?= date("Y-m-d H:i:s", strtotime($log['action_time'])) ?></small>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <button class="pagination-btn <?= ($i == $page) ? 'active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></button>
        <?php endfor; ?>
    </div>

<?php else: ?>
    <p>Nav veiktu darbību.</p>
<?php endif; ?>
