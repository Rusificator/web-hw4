<?php
header('Content-Type: text/html; charset=UTF-8');

// Если пользователь уже авторизован – перенаправляем на главную
session_start();
if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

// Обработка POST-запроса (отправка формы логина)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $pass = $_POST['pass'] ?? '';

    $error = '';
    if (empty($login) || empty($pass)) {
        $error = 'Заполните оба поля.';
    } else {
        // Подключаемся к БД (можно использовать ту же функцию getDB, что и в index.php)
        require_once 'index.php'; // или продублировать функцию getDB
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, login, password_hash FROM application WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($pass, $user['password_hash'])) {
            // Успешный вход
            $_SESSION['login'] = $user['login'];
            $_SESSION['uid'] = $user['id'];  // ID анкеты
            header('Location: index.php');
            exit();
        } else {
            $error = 'Неверный логин или пароль.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Вход для редактирования анкеты</title>
    <link rel="stylesheet" href="style.css">
      <style>
        .nav-buttons {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .nav-buttons a {
            display: inline-block;
            background-color: #eb4200;
            color: white;
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .nav-buttons a:hover {
            background-color: #eb4200;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Вход в систему</h1>
    <?php if (!empty($error)): ?>
        <div class="errors"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Логин</label>
            <input type="text" name="login" required>
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="pass" required>
        </div>
        <button type="submit">Войти</button>
    </form>
     <div class="nav-buttons">
        <a href="index.php">Вернуться к анкете</a>
    </div>
</div>
</body>
</html>