<?php
$db_user = 'u82457';
$db_pass = '7777166';
$db_name = 'u82457';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("
        SELECT a.*, GROUP_CONCAT(l.name SEPARATOR ', ') AS languages
        FROM application a
        LEFT JOIN application_language al ON a.id = al.application_id
        LEFT JOIN language l ON al.language_id = l.id
        GROUP BY a.id
        ORDER BY a.id DESC
    ");
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Сохранённые анкеты</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
         .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            background-color: #eb4200;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .back-link a:hover {
            background-color: #eb4200;
        }
    </style>
</head>
<body>
    <h1>Сохранённые анкеты</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Email</th>
            <th>Дата рождения</th>
            <th>Пол</th>
            <th>Биография</th>
            <th>Согласие</th>
            <th>Языки</th>
            <th>Дата создания</th>
        </tr>
        <?php foreach ($applications as $app): ?>
        <tr>
            <td><?= htmlspecialchars($app['id']) ?></td>
            <td><?= htmlspecialchars($app['full_name']) ?></td>
            <td><?= htmlspecialchars($app['phone']) ?></td>
            <td><?= htmlspecialchars($app['email']) ?></td>
            <td><?= htmlspecialchars($app['birth_date']) ?></td>
            <td><?= htmlspecialchars($app['gender']) ?></td>
            <td><?= nl2br(htmlspecialchars($app['biography'])) ?></td>
            <td><?= $app['contract_accepted'] ? 'Да' : 'Нет' ?></td>
            <td><?= htmlspecialchars($app['languages']) ?></td>
            <td><?= htmlspecialchars($app['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div class="back-link">
            <a href="index.php">← Вернуться к форме</a>
        </div>
</body>
</html>