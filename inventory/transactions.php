<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_login();
$title = 'Inventory transactions';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $itemId = filter_input(INPUT_POST, 'inventory_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $type = (string) ($_POST['type'] ?? '');
    $notes = trim((string) ($_POST['notes'] ?? ''));
    if (!$itemId || !$quantity || $quantity < 1 || !in_array($type, ['Stock in', 'Stock out'], true)) {
        $error = 'Choose an item, a valid transaction type, and a quantity greater than zero.';
    } else {
        $pdo->beginTransaction();
        $itemQuery = $pdo->prepare('SELECT id, quantity FROM inventory WHERE id = ? FOR UPDATE');
        $itemQuery->execute([$itemId]);
        $item = $itemQuery->fetch();
        if (!$item || ($type === 'Stock out' && (int) $item['quantity'] < $quantity)) {
            $pdo->rollBack();
            $error = !$item ? 'The selected inventory item no longer exists.' : 'There is not enough stock for this issue.';
        } else {
            $difference = $type === 'Stock in' ? $quantity : -$quantity;
            $update = $pdo->prepare('UPDATE inventory SET quantity = quantity + ? WHERE id = ?');
            $update->execute([$difference, $itemId]);
            $insert = $pdo->prepare('INSERT INTO inventory_transactions (inventory_id, type, quantity, transaction_date, notes) VALUES (?, ?, ?, NOW(), ?)');
            $insert->execute([$itemId, $type, $quantity, $notes !== '' ? $notes : null]);
            $pdo->commit();
            set_flash('success', 'Stock transaction recorded.');
            redirect('inventory/transactions.php');
        }
    }
}
$items = $pdo->query('SELECT id, name, quantity, unit FROM inventory ORDER BY name')->fetchAll();
$transactions = $pdo->query('SELECT t.*, i.name AS item_name, i.unit FROM inventory_transactions t JOIN inventory i ON i.id = t.inventory_id ORDER BY t.transaction_date DESC, t.id DESC LIMIT 100')->fetchAll();
require APP_ROOT . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">INVENTORY</p><h1>Stock transactions</h1><p class="muted">Record stock received or used. On-hand quantity updates automatically.</p></div><a class="button button-secondary" href="<?= e(app_url('inventory/index.php')) ?>">Back to inventory</a></div>
<section class="panel form-panel transaction-panel">
    <h2>Record a transaction</h2>
    <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
        <div class="field"><label for="inventory_id">Inventory item *</label><select name="inventory_id" id="inventory_id" required><option value="">Select an item...</option><?php foreach ($items as $item): ?><option value="<?= (int) $item['id'] ?>"><?= e($item['name'] . ' · ' . $item['quantity'] . ' ' . $item['unit'] . ' on hand') ?></option><?php endforeach; ?></select></div>
        <div class="field"><label for="type">Transaction *</label><select name="type" id="type" required><option value="Stock in">Stock in (received)</option><option value="Stock out">Stock out (used)</option></select></div>
        <div class="field"><label for="quantity">Quantity *</label><input id="quantity" name="quantity" type="number" min="1" step="1" required></div>
        <div class="field"><label for="notes">Notes</label><input id="notes" name="notes" type="text"></div>
    </div><div class="form-actions"><button class="button button-primary" type="submit">Save transaction</button></div></form>
</section>
<section class="panel dashboard-panel"><div class="section-heading"><div><p class="eyebrow">HISTORY</p><h2>Recent transactions</h2></div></div><div class="table-wrap"><table><thead><tr><th>Item</th><th>Type</th><th>Quantity</th><th>Date</th><th>Notes</th></tr></thead><tbody>
<?php foreach ($transactions as $transaction): ?><tr><td><?= e($transaction['item_name']) ?></td><td><span class="status-pill"><?= e($transaction['type']) ?></span></td><td><?= (int) $transaction['quantity'] ?> <?= e($transaction['unit']) ?></td><td><?= e(format_date($transaction['transaction_date'])) ?></td><td><?= e($transaction['notes'] ?: '—') ?></td></tr><?php endforeach; ?>
<?php if (!$transactions): ?><tr><td colspan="5" class="empty-state">No stock transactions have been recorded.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require APP_ROOT . '/includes/footer.php'; ?>
