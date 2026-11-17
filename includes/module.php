<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_login();

function module_definitions(): array
{
    static $modules = [
        'rooms' => [
            'title' => 'Rooms', 'table' => 'rooms',
            'fields' => [
                'room_number' => ['Room number', 'text', 'required'],
                'floor' => ['Floor', 'number', 'required'],
                'room_type' => ['Room type', 'text', 'required'],
                'status' => ['Status', 'select', ['Available', 'Occupied', 'Needs cleaning', 'Out of service']],
                'notes' => ['Notes', 'textarea'],
            ],
            'search' => ['room_number', 'room_type', 'status'],
            'columns' => ['room_number' => 'Room', 'floor' => 'Floor', 'room_type' => 'Type', 'status' => 'Status'],
            'order' => 'room_number',
        ],
        'cleaning' => [
            'title' => 'Cleaning tasks', 'table' => 'cleaning_tasks',
            'fields' => [
                'room_id' => ['Room', 'select_db', 'rooms', 'room_number'],
                'assigned_staff_id' => ['Assigned staff', 'select_db', 'staff', 'name', true],
                'scheduled_at' => ['Scheduled for', 'datetime-local', 'required'],
                'status' => ['Status', 'select', ['Scheduled', 'In progress', 'Completed', 'Cancelled']],
                'notes' => ['Notes', 'textarea'],
            ],
            'search' => ['status', 'notes'],
            'columns' => ['room_id' => 'Room', 'assigned_staff_id' => 'Assigned to', 'scheduled_at' => 'Scheduled', 'status' => 'Status'],
            'lookups' => ['room_id' => ['rooms', 'room_number'], 'assigned_staff_id' => ['staff', 'name']],
            'order' => 'scheduled_at',
        ],
        'inspections' => [
            'title' => 'Inspections', 'table' => 'inspections',
            'fields' => [
                'room_id' => ['Room', 'select_db', 'rooms', 'room_number'],
                'inspector_id' => ['Inspector', 'select_db', 'staff', 'name', true],
                'inspection_date' => ['Inspection date', 'date', 'required'],
                'cleanliness' => ['Cleanliness score (1–5)', 'number', 'required'],
                'safety' => ['Safety score (1–5)', 'number', 'required'],
                'status' => ['Result', 'select', ['Passed', 'Needs follow-up', 'Failed']],
                'notes' => ['Notes', 'textarea'],
            ],
            'search' => ['status', 'notes'],
            'columns' => ['room_id' => 'Room', 'inspector_id' => 'Inspector', 'inspection_date' => 'Date', 'status' => 'Result'],
            'lookups' => ['room_id' => ['rooms', 'room_number'], 'inspector_id' => ['staff', 'name']],
            'order' => 'inspection_date',
        ],
        'maintenance_requests' => [
            'title' => 'Maintenance requests', 'table' => 'maintenance_requests',
            'fields' => [
                'room_id' => ['Room', 'select_db', 'rooms', 'room_number'],
                'requested_by' => ['Requested by', 'text', 'required'],
                'assigned_staff_id' => ['Assigned staff', 'select_db', 'staff', 'name', true],
                'issue' => ['Issue', 'textarea', 'required'],
                'priority' => ['Priority', 'select', ['Low', 'Normal', 'High', 'Urgent']],
                'status' => ['Status', 'select', ['Open', 'In progress', 'On hold', 'Resolved', 'Closed']],
                'request_date' => ['Request date', 'date', 'required'],
            ],
            'search' => ['requested_by', 'issue', 'priority', 'status'],
            'columns' => ['room_id' => 'Room', 'issue' => 'Issue', 'priority' => 'Priority', 'status' => 'Status', 'request_date' => 'Requested'],
            'lookups' => ['room_id' => ['rooms', 'room_number']],
            'order' => 'request_date',
        ],
        'work_orders' => [
            'title' => 'Work orders', 'table' => 'work_orders',
            'fields' => [
                'request_id' => ['Related request', 'select_db', 'maintenance_requests', 'issue', true],
                'room_id' => ['Room', 'select_db', 'rooms', 'room_number'],
                'assigned_staff_id' => ['Assigned staff', 'select_db', 'staff', 'name', true],
                'title' => ['Title', 'text', 'required'],
                'description' => ['Description', 'textarea'],
                'priority' => ['Priority', 'select', ['Low', 'Normal', 'High', 'Urgent']],
                'status' => ['Status', 'select', ['Open', 'In progress', 'On hold', 'Completed', 'Cancelled']],
                'due_date' => ['Due date', 'date'],
                'completed_at' => ['Completed at', 'datetime-local'],
            ],
            'search' => ['title', 'description', 'priority', 'status'],
            'columns' => ['title' => 'Work order', 'room_id' => 'Room', 'assigned_staff_id' => 'Assigned to', 'priority' => 'Priority', 'status' => 'Status', 'due_date' => 'Due'],
            'lookups' => ['room_id' => ['rooms', 'room_number'], 'assigned_staff_id' => ['staff', 'name']],
            'order' => 'created_at',
        ],
        'repairs' => [
            'title' => 'Repairs', 'table' => 'repairs',
            'fields' => [
                'work_order_id' => ['Work order', 'select_db', 'work_orders', 'title', true],
                'room_id' => ['Room', 'select_db', 'rooms', 'room_number'],
                'technician_id' => ['Technician', 'select_db', 'staff', 'name', true],
                'repair_date' => ['Repair date', 'date', 'required'],
                'description' => ['Repair details', 'textarea', 'required'],
                'cost' => ['Cost', 'number', 'required'],
            ],
            'search' => ['description'],
            'columns' => ['work_order_id' => 'Work order', 'room_id' => 'Room', 'technician_id' => 'Technician', 'repair_date' => 'Date', 'cost' => 'Cost'],
            'lookups' => ['work_order_id' => ['work_orders', 'title'], 'room_id' => ['rooms', 'room_number'], 'technician_id' => ['staff', 'name']],
            'order' => 'repair_date',
        ],
        'equipment' => [
            'title' => 'Equipment', 'table' => 'equipment',
            'fields' => [
                'name' => ['Name', 'text', 'required'],
                'category' => ['Category', 'text', 'required'],
                'serial_number' => ['Serial number', 'text'],
                'location' => ['Location', 'text'],
                'condition' => ['Condition', 'select', ['Good', 'Fair', 'Needs repair', 'Out of service']],
                'purchase_date' => ['Purchase date', 'date'],
                'next_service_date' => ['Next service date', 'date'],
            ],
            'search' => ['name', 'category', 'serial_number', 'location', 'condition'],
            'columns' => ['name' => 'Equipment', 'category' => 'Category', 'serial_number' => 'Serial', 'location' => 'Location', 'condition' => 'Condition', 'next_service_date' => 'Next service'],
            'order' => 'name',
        ],
        'inventory' => [
            'title' => 'Inventory', 'table' => 'inventory',
            'fields' => [
                'name' => ['Item name', 'text', 'required'],
                'sku' => ['SKU', 'text'],
                'quantity' => ['Quantity on hand', 'number', 'required'],
                'unit' => ['Unit', 'text', 'required'],
                'reorder_level' => ['Reorder level', 'number', 'required'],
                'location' => ['Storage location', 'text'],
            ],
            'search' => ['name', 'sku', 'location'],
            'columns' => ['name' => 'Item', 'sku' => 'SKU', 'quantity' => 'On hand', 'unit' => 'Unit', 'reorder_level' => 'Reorder at', 'location' => 'Location'],
            'order' => 'name',
        ],
        'lost_found' => [
            'title' => 'Lost & found', 'table' => 'lost_found',
            'fields' => [
                'item_name' => ['Item', 'text', 'required'],
                'description' => ['Description', 'textarea'],
                'location_found' => ['Location found', 'text'],
                'found_date' => ['Date found', 'date', 'required'],
                'reported_by' => ['Reported by', 'text'],
                'status' => ['Status', 'select', ['In storage', 'Claimed', 'Returned', 'Donated']],
                'claimed_by' => ['Claimed by', 'text'],
            ],
            'search' => ['item_name', 'description', 'location_found', 'status', 'reported_by'],
            'columns' => ['item_name' => 'Item', 'location_found' => 'Location', 'found_date' => 'Found', 'reported_by' => 'Reported by', 'status' => 'Status'],
            'order' => 'found_date',
        ],
        'schedules' => [
            'title' => 'Staff schedules', 'table' => 'schedules',
            'fields' => [
                'staff_id' => ['Staff member', 'select_db', 'staff', 'name'],
                'shift_date' => ['Shift date', 'date', 'required'],
                'start_time' => ['Start time', 'time', 'required'],
                'end_time' => ['End time', 'time', 'required'],
                'department' => ['Department', 'text', 'required'],
                'notes' => ['Notes', 'textarea'],
            ],
            'search' => ['department', 'notes'],
            'columns' => ['staff_id' => 'Staff member', 'shift_date' => 'Date', 'start_time' => 'Start', 'end_time' => 'End', 'department' => 'Department'],
            'lookups' => ['staff_id' => ['staff', 'name']],
            'order' => 'shift_date',
        ],
        'staff' => [
            'title' => 'Staff', 'table' => 'staff',
            'fields' => [
                'name' => ['Full name', 'text', 'required'],
                'email' => ['Email', 'email', 'required'],
                'phone' => ['Phone', 'tel'],
                'department' => ['Department', 'text', 'required'],
                'job_title' => ['Job title', 'text', 'required'],
                'status' => ['Status', 'select', ['Active', 'On leave', 'Inactive']],
                'hire_date' => ['Hire date', 'date'],
            ],
            'search' => ['name', 'email', 'phone', 'department', 'job_title', 'status'],
            'columns' => ['name' => 'Name', 'email' => 'Email', 'department' => 'Department', 'job_title' => 'Job title', 'status' => 'Status'],
            'order' => 'name',
        ],
    ];

    return $modules;
}

function module_definition(string $key): array
{
    $modules = module_definitions();
    if (!isset($modules[$key])) {
        http_response_code(404);
        exit('Module not found.');
    }

    return $modules[$key];
}

function module_record(array $module, int $id): ?array
{
    global $pdo;
    $statement = $pdo->prepare('SELECT * FROM `' . $module['table'] . '` WHERE id = ?');
    $statement->execute([$id]);
    $record = $statement->fetch();

    return $record ?: null;
}

function validate_module_values(array $module, array &$values): ?string
{
    global $pdo;

    foreach ($module['fields'] as $name => $definition) {
        [$label, $type] = $definition;
        $value = $values[$name];
        $required = $type === 'select_db'
            ? !($definition[4] ?? false)
            : ($type === 'select' || in_array('required', $definition, true));

        if ($value === null) {
            if ($required) {
                return "{$label} is required.";
            }
            continue;
        }

        if ($type === 'select' && !in_array($value, $definition[2], true)) {
            return "Choose a valid value for {$label}.";
        }

        if ($type === 'select_db') {
            $referenceId = filter_var($value, FILTER_VALIDATE_INT);
            if ($referenceId === false || $referenceId < 1) {
                return "Choose a valid value for {$label}.";
            }
            $lookup = $pdo->prepare('SELECT COUNT(*) FROM `' . $definition[2] . '` WHERE id = ?');
            $lookup->execute([$referenceId]);
            if ((int) $lookup->fetchColumn() === 0) {
                return "The selected {$label} no longer exists.";
            }
            $values[$name] = $referenceId;
        }

        if ($type === 'number') {
            if ($name === 'cost') {
                if (!is_numeric($value) || (float) $value < 0) {
                    return "{$label} must be a non-negative amount.";
                }
                $values[$name] = (float) $value;
            } else {
                $number = filter_var($value, FILTER_VALIDATE_INT);
                if ($number === false) {
                    return "{$label} must be a whole number.";
                }
                if (in_array($name, ['quantity', 'reorder_level'], true) && $number < 0) {
                    return "{$label} cannot be negative.";
                }
                if (in_array($name, ['cleanliness', 'safety'], true) && ($number < 1 || $number > 5)) {
                    return "{$label} must be between 1 and 5.";
                }
                $values[$name] = $number;
            }
        }

        if ($type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "Enter a valid email address for {$label}.";
        }

        if ($type === 'date') {
            $date = DateTime::createFromFormat('!Y-m-d', $value);
            if (!$date || $date->format('Y-m-d') !== $value) {
                return "Enter a valid date for {$label}.";
            }
        }

        if ($type === 'time') {
            $time = DateTime::createFromFormat('!H:i', $value);
            if (!$time || $time->format('H:i') !== $value) {
                return "Enter a valid time for {$label}.";
            }
        }

        if ($type === 'datetime-local') {
            $dateTime = DateTime::createFromFormat('!Y-m-d H:i:s', $value);
            if (!$dateTime || $dateTime->format('Y-m-d H:i:s') !== $value) {
                return "Enter a valid date and time for {$label}.";
            }
        }
    }

    return null;
}

function module_page(string $key, string $action): void
{
    global $pdo;
    $module = module_definition($key);
    $title = $module['title'];
    $base = $key;
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
    $record = $id > 0 ? module_record($module, $id) : null;
    $formError = '';
    $formValues = [];

    if ($action !== 'index' && $action !== 'create' && !$record) {
        http_response_code(404);
        exit('Record not found.');
    }

    if ($action === 'index' && ($_GET['action'] ?? '') === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $statement = $pdo->prepare('DELETE FROM `' . $module['table'] . '` WHERE id = ?');
        $statement->execute([$id]);
        set_flash('success', rtrim($title, 's') . ' deleted.');
        redirect($base . '/index.php');
    }

    if (($action === 'create' || $action === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $values = [];
        foreach ($module['fields'] as $name => $definition) {
            $value = trim((string) ($_POST[$name] ?? ''));
            if ($value === '') {
                $value = null;
            }
            if ($value !== null && $definition[1] === 'datetime-local') {
                $value = str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
            }
            $values[$name] = $value;
        }

        $formError = validate_module_values($module, $values) ?? '';
        $formValues = $values;
        if ($formError === '') {
            if ($action === 'create') {
                $columns = array_keys($values);
                $sql = 'INSERT INTO `' . $module['table'] . '` (`' . implode('`, `', $columns) . '`) VALUES ('
                    . implode(', ', array_fill(0, count($columns), '?')) . ')';
                $statement = $pdo->prepare($sql);
                $statement->execute(array_values($values));
                set_flash('success', rtrim($title, 's') . ' created.');
                redirect($base . '/index.php');
            }

            $assignments = array_map(static fn(string $column): string => "`{$column}` = ?", array_keys($values));
            $statement = $pdo->prepare('UPDATE `' . $module['table'] . '` SET ' . implode(', ', $assignments) . ' WHERE id = ?');
            $statement->execute([...array_values($values), $id]);
            set_flash('success', rtrim($title, 's') . ' updated.');
            redirect($base . '/index.php');
        }
    }

    require APP_ROOT . '/includes/header.php';
    if ($action === 'index') {
        $query = trim((string) ($_GET['q'] ?? ''));
        $sql = 'SELECT * FROM `' . $module['table'] . '`';
        $parameters = [];
        if ($query !== '' && !empty($module['search'])) {
            $conditions = array_map(static fn(string $column): string => "`{$column}` LIKE ?", $module['search']);
            $sql .= ' WHERE ' . implode(' OR ', $conditions);
            $parameters = array_fill(0, count($conditions), '%' . $query . '%');
        }
        $sql .= ' ORDER BY `' . $module['order'] . '` DESC LIMIT 200';
        $statement = $pdo->prepare($sql);
        $statement->execute($parameters);
        $rows = $statement->fetchAll();
        ?>
        <div class="page-heading">
            <div><p class="eyebrow">OPERATIONS</p><h1><?= e($title) ?></h1><p class="muted">Manage <?= e(strtolower($title)) ?> across your property.</p></div>
            <div class="heading-actions">
                <?php if ($key === 'inventory'): ?><a class="button button-secondary" href="<?= e(app_url('inventory/transactions.php')) ?>">Stock transactions</a><?php endif; ?>
                <a class="button button-primary" href="<?= e(app_url($base . '/create.php')) ?>">＋ Add <?= e(rtrim($title, 's')) ?></a>
            </div>
        </div>
        <section class="panel">
            <form class="search-form" method="get">
                <label class="sr-only" for="search">Search <?= e(strtolower($title)) ?></label>
                <input id="search" type="search" name="q" value="<?= e($query) ?>" placeholder="Search <?= e(strtolower($title)) ?>...">
                <button class="button button-secondary" type="submit">Search</button>
                <?php if ($query !== ''): ?><a class="button button-quiet" href="<?= e(app_url($base . '/index.php')) ?>">Clear</a><?php endif; ?>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><?php foreach ($module['columns'] as $label): ?><th><?= e($label) ?></th><?php endforeach; ?><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                        <?php foreach ($module['columns'] as $column => $label): ?>
                            <?php $value = $row[$column] ?? ''; if (isset($module['lookups'][$column]) && $value !== '') {
                                [$lookupTable, $lookupColumn] = $module['lookups'][$column];
                                $lookup = $pdo->prepare("SELECT `{$lookupColumn}` FROM `{$lookupTable}` WHERE id = ?");
                                $lookup->execute([$value]);
                                $value = $lookup->fetchColumn() ?: '—';
                            } ?>
                            <td><?= e($value === '' ? '—' : $value) ?></td>
                        <?php endforeach; ?>
                            <td class="actions">
                                <a href="<?= e(app_url($base . '/view.php?id=' . (int) $row['id'])) ?>">View</a>
                                <a href="<?= e(app_url($base . '/edit.php?id=' . (int) $row['id'])) ?>">Edit</a>
                                <form method="post" action="<?= e(app_url($base . '/index.php?action=delete&id=' . (int) $row['id'])) ?>" onsubmit="return confirm('Delete this record?')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <button class="link-button danger-text" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?><tr><td class="empty-state" colspan="<?= count($module['columns']) + 1 ?>">No records found. Add your first record to get started.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p class="table-note">Showing up to 200 records · <?= count($rows) ?> shown</p>
        </section>
        <?php
    } elseif ($action === 'view') {
        ?>
        <div class="page-heading"><div><p class="eyebrow"><?= e(strtoupper($title)) ?></p><h1>Record details</h1></div><a class="button button-secondary" href="<?= e(app_url($base . '/index.php')) ?>">Back to list</a></div>
        <section class="panel detail-grid">
            <?php foreach ($module['fields'] as $name => $definition): ?>
                <?php $value = $record[$name] ?? null; if (isset($module['lookups'][$name]) && $value !== null) {
                    [$lookupTable, $lookupColumn] = $module['lookups'][$name];
                    $lookup = $pdo->prepare("SELECT `{$lookupColumn}` FROM `{$lookupTable}` WHERE id = ?");
                    $lookup->execute([$value]);
                    $value = $lookup->fetchColumn() ?: '—';
                } ?>
                <div class="detail-item"><span><?= e($definition[0]) ?></span><strong><?= nl2br(e($value ?? '—')) ?></strong></div>
            <?php endforeach; ?>
            <div class="detail-actions"><a class="button button-primary" href="<?= e(app_url($base . '/edit.php?id=' . $id)) ?>">Edit record</a></div>
        </section>
        <?php
    } else {
        $isEdit = $action === 'edit';
        ?>
        <div class="page-heading"><div><p class="eyebrow"><?= e(strtoupper($title)) ?></p><h1><?= $isEdit ? 'Edit record' : 'Add ' . e(rtrim($title, 's')) ?></h1><p class="muted">Complete the details below.</p></div><a class="button button-secondary" href="<?= e(app_url($base . '/index.php')) ?>">Cancel</a></div>
        <form class="panel form-panel" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <?php if ($formError !== ''): ?><div class="alert alert-error"><?= e($formError) ?></div><?php endif; ?>
            <div class="form-grid">
            <?php foreach ($module['fields'] as $name => $definition):
                [$label, $type] = $definition;
                $required = $type === 'select_db'
                    ? !($definition[4] ?? false)
                    : in_array('required', $definition, true);
                $value = array_key_exists($name, $formValues)
                    ? ($formValues[$name] ?? '')
                    : ($record[$name] ?? '');
                if ($type === 'select' && $value === '' && !$isEdit) {
                    $value = $definition[2][0];
                }
                if ($type === 'datetime-local' && $value !== '') {
                    $value = date('Y-m-d\TH:i', strtotime((string) $value));
                }
                if ($type === 'time' && $value !== '') {
                    $value = substr((string) $value, 0, 5);
                }
                $wide = $type === 'textarea' ? ' field-wide' : '';
                ?>
                <div class="field<?= $wide ?>">
                    <label for="<?= e($name) ?>"><?= e($label) ?><?= $required ? ' *' : '' ?></label>
                    <?php if ($type === 'textarea'): ?>
                        <textarea id="<?= e($name) ?>" name="<?= e($name) ?>" rows="4" <?= $required ? 'required' : '' ?>><?= e($value) ?></textarea>
                    <?php elseif ($type === 'select'): ?>
                        <select id="<?= e($name) ?>" name="<?= e($name) ?>" <?= $required ? 'required' : '' ?>>
                            <option value="">Select...</option>
                            <?php foreach ($definition[2] as $option): ?><option value="<?= e($option) ?>" <?= (string) $value === $option ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?>
                        </select>
                    <?php elseif ($type === 'select_db'):
                        $lookupTable = $definition[2];
                        $lookupColumn = $definition[3];
                        $allowEmpty = $definition[4] ?? false;
                        $options = $pdo->query("SELECT id, `{$lookupColumn}` AS label FROM `{$lookupTable}` ORDER BY `{$lookupColumn}`")->fetchAll();
                        ?>
                        <select id="<?= e($name) ?>" name="<?= e($name) ?>" <?= $required ? 'required' : '' ?>>
                            <option value=""><?= $allowEmpty ? 'None' : 'Select...' ?></option>
                            <?php foreach ($options as $option): ?><option value="<?= (int) $option['id'] ?>" <?= (string) $value === (string) $option['id'] ? 'selected' : '' ?>><?= e($option['label']) ?></option><?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input id="<?= e($name) ?>" name="<?= e($name) ?>" type="<?= e($type) ?>" value="<?= e($value) ?>" <?= $required ? 'required' : '' ?> <?= $type === 'number' && str_contains($name, 'score') ? 'min="1" max="5" step="1"' : '' ?> <?= $name === 'cost' ? 'min="0" step="0.01"' : '' ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
            <div class="form-actions"><button class="button button-primary" type="submit"><?= $isEdit ? 'Save changes' : 'Create record' ?></button><a class="button button-quiet" href="<?= e(app_url($base . '/index.php')) ?>">Cancel</a></div>
        </form>
        <?php
    }
    require APP_ROOT . '/includes/footer.php';
}
