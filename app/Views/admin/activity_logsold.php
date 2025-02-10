<!DOCTYPE html>
<html lang="id">
<head>
    <title>Activity Log</title>
</head>
<body>
    <h2>Activity Log Pengguna</h2>

    <!-- Form Filter -->
    <form method="GET">
        <label for="user_id">User:</label>
        <select name="user_id" id="user_id">
            <option value="">Semua</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>" <?= (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : '' ?>>
                    <?= $user['username'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="start_date">Dari Tanggal:</label>
        <input type="date" name="start_date" id="start_date" value="<?= $_GET['start_date'] ?? '' ?>">

        <label for="end_date">Sampai Tanggal:</label>
        <input type="date" name="end_date" id="end_date" value="<?= $_GET['end_date'] ?? '' ?>">

        <button type="submit">Filter</button>
        <a href="<?= base_url('admin/activity-logs') ?>">Reset</a>
    </form>

    <!-- Tabel Log -->
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Method</th>
                <th>Endpoint</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td><?= $log['username'] ?? '-' ?></td>
                    <td><?= $log['method'] ?></td>
                    <td><?= $log['endpoint'] ?></td>
                    <td><?= $log['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
