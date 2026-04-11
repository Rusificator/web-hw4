<?php
header('Content-Type: text/html; charset=UTF-8');

// Функция подключения к БД
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $db_host = 'localhost';
        $db_user = 'u82457';
        $db_pass = '7777166';
        $db_name = 'u82457';
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к БД: " . $e->getMessage());
        }
    }
    return $pdo;
}

$allowed_languages = [
    'Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python',
    'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'
];
$allowed_genders = ['male', 'female'];

// ========================= GET-ЗАПРОС =========================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $messages = [];
    $errors = [];
    $values = [];

    // Запускаем сессию, чтобы проверить авторизацию
    session_start();
    $is_logged_in = !empty($_SESSION['login']);

    $fields = ['full_name', 'phone', 'email', 'birth_date', 'gender', 'biography', 'contract_accepted', 'languages'];

    if (!$is_logged_in) {
        // ---- Неавторизованный пользователь: работаем с cookies ----
        foreach ($fields as $field) {
            $errors[$field] = !empty($_COOKIE[$field . '_error']);
        }

        // Сообщения об ошибках
        if ($errors['full_name']) $messages[] = '<div class="error-message">ФИО должно содержать только буквы и пробелы (макс. 150 символов).</div>';
        if ($errors['phone']) $messages[] = '<div class="error-message">Телефон должен содержать от 6 до 12 цифр, допускаются символы +, -, (, ), пробел.</div>';
        if ($errors['email']) $messages[] = '<div class="error-message">Введите корректный email.</div>';
        if ($errors['birth_date']) $messages[] = '<div class="error-message">Дата рождения должна быть в формате ГГГГ-ММ-ДД и не позже сегодняшнего дня.</div>';
        if ($errors['gender']) $messages[] = '<div class="error-message">Выберите пол.</div>';
        if ($errors['biography']) $messages[] = '<div class="error-message">Биография не должна превышать 10000 символов.</div>';
        if ($errors['contract_accepted']) $messages[] = '<div class="error-message">Необходимо подтвердить согласие.</div>';
        if ($errors['languages']) $messages[] = '<div class="error-message">Выберите хотя бы один язык программирования из списка.</div>';

        // Значения из cookies
        foreach ($fields as $field) {
            $values[$field] = empty($_COOKIE[$field . '_value']) ? '' : $_COOKIE[$field . '_value'];
        }
        if (!empty($_COOKIE['languages_value'])) {
            $values['languages'] = explode(',', $_COOKIE['languages_value']);
        } else {
            $values['languages'] = [];
        }
        $values['contract_accepted'] = !empty($_COOKIE['contract_accepted_value']) ? true : false;

        // Сообщение об успехе
        if (!empty($_COOKIE['save'])) {
            setcookie('save', '', 1);
            $messages[] = '<div class="success-message">Данные успешно сохранены!</div>';
            if (!empty($_COOKIE['login']) && !empty($_COOKIE['pass'])) {
                $messages[] = '<div class="success-message">Ваш логин: <strong>' . htmlspecialchars($_COOKIE['login']) . '</strong>, пароль: <strong>' . htmlspecialchars($_COOKIE['pass']) . '</strong>. <a href="login.php">Войти</a> для редактирования.</div>';
                setcookie('login', '', 1);
                setcookie('pass', '', 1);
            }
        }
    } else {
        // ---- Авторизованный пользователь: загружаем данные из БД ----
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT * FROM application WHERE id = ?");
            $stmt->execute([$_SESSION['uid']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $values['full_name'] = htmlspecialchars($user['full_name']);
                $values['phone'] = htmlspecialchars($user['phone']);
                $values['email'] = htmlspecialchars($user['email']);
                $values['birth_date'] = htmlspecialchars($user['birth_date']);
                $values['gender'] = $user['gender'];
                $values['biography'] = htmlspecialchars($user['biography']);
                $values['contract_accepted'] = (bool)$user['contract_accepted'];
                // Языки
                $stmt_lang = $pdo->prepare("
                    SELECT l.name FROM application_language al
                    JOIN language l ON al.language_id = l.id
                    WHERE al.application_id = ?
                ");
                $stmt_lang->execute([$_SESSION['uid']]);
                $values['languages'] = [];
                while ($row = $stmt_lang->fetch(PDO::FETCH_ASSOC)) {
                    $values['languages'][] = $row['name'];
                }
                // Обнуляем ошибки – они не нужны
                $errors = array_fill_keys($fields, false);
                $messages[] = '<div class="success-message">Вы вошли как ' . htmlspecialchars($_SESSION['login']) . '. Можете редактировать свои данные.</div>';
            } else {
                // Ошибка: пользователь не найден
                $messages[] = '<div class="error-message">Ошибка: данные пользователя не найдены. Попробуйте выйти и войти снова.</div>';
                session_destroy();
                header('Location: index.php');
                exit();
            }
        } catch (Exception $e) {
            $messages[] = '<div class="error-message">Ошибка загрузки данных: ' . $e->getMessage() . '</div>';
            $values = array_fill_keys($fields, '');
            $values['languages'] = [];
            $errors = array_fill_keys($fields, false);
        }
    }

    // Список языков для выпадающего списка
    try {
        $pdo = getDB();
        $languages_from_db = [];
        $stmt = $pdo->query("SELECT name FROM language ORDER BY name");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $languages_from_db[] = $row['name'];
        }
        if (empty($languages_from_db)) {
            $languages_from_db = $allowed_languages;
        }
    } catch (Exception $e) {
        $languages_from_db = $allowed_languages;
        $messages[] = '<div class="error-message">Ошибка получения списка языков: ' . $e->getMessage() . '</div>';
    }

    include 'form.php';
    exit();
}

// ========================= POST-ЗАПРОС =========================
else {
    $errors = false;

    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birth_date = trim($_POST['birth_date'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $biography = trim($_POST['biography'] ?? '');
    $contract_accepted = isset($_POST['contract_accepted']) ? 1 : 0;
    $languages = $_POST['languages'] ?? [];

    // Валидация (та же, что в лабе 4)
    if (empty($full_name)) {
        setcookie('full_name_error', '1', time() + 24*3600);
        $errors = true;
    } elseif (!preg_match('/^[а-яА-Яa-zA-Z\s]+$/u', $full_name)) {
        setcookie('full_name_error', '1', time() + 24*3600);
        $errors = true;
    } elseif (strlen($full_name) > 150) {
        setcookie('full_name_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('full_name_value', $full_name, time() + 30*24*3600);

    if (empty($phone)) {
        setcookie('phone_error', '1', time() + 24*3600);
        $errors = true;
    } elseif (!preg_match('/^[\d\s\-\+\(\)]{6,12}$/', $phone)) {
        setcookie('phone_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('phone_value', $phone, time() + 30*24*3600);

    if (empty($email)) {
        setcookie('email_error', '1', time() + 24*3600);
        $errors = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('email_value', $email, time() + 30*24*3600);

    if (empty($birth_date)) {
        setcookie('birth_date_error', '1', time() + 24*3600);
        $errors = true;
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $birth_date);
        if (!$date || $date->format('Y-m-d') !== $birth_date) {
            setcookie('birth_date_error', '1', time() + 24*3600);
            $errors = true;
        } elseif ($date > new DateTime('today')) {
            setcookie('birth_date_error', '1', time() + 24*3600);
            $errors = true;
        }
    }
    setcookie('birth_date_value', $birth_date, time() + 30*24*3600);

    if (empty($gender)) {
        setcookie('gender_error', '1', time() + 24*3600);
        $errors = true;
    } elseif (!in_array($gender, $allowed_genders)) {
        setcookie('gender_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('gender_value', $gender, time() + 30*24*3600);

    if (strlen($biography) > 10000) {
        setcookie('biography_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('biography_value', $biography, time() + 30*24*3600);

    if (!$contract_accepted) {
        setcookie('contract_accepted_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('contract_accepted_value', $contract_accepted ? '1' : '0', time() + 30*24*3600);

    if (empty($languages)) {
        setcookie('languages_error', '1', time() + 24*3600);
        $errors = true;
    } else {
        foreach ($languages as $lang) {
            if (!in_array($lang, $allowed_languages)) {
                setcookie('languages_error', '1', time() + 24*3600);
                $errors = true;
                break;
            }
        }
    }
    setcookie('languages_value', implode(',', $languages), time() + 30*24*3600);

    if ($errors) {
        header('Location: index.php');
        exit();
    }

    // Сохранение в БД
    try {
        $pdo = getDB();
        $pdo->beginTransaction();

        session_start();
        $is_logged_in = !empty($_SESSION['login']);

        if ($is_logged_in) {
            // Обновление
            $stmt = $pdo->prepare("
                UPDATE application SET
                    full_name = :full_name,
                    phone = :phone,
                    email = :email,
                    birth_date = :birth_date,
                    gender = :gender,
                    biography = :biography,
                    contract_accepted = :contract_accepted
                WHERE id = :id
            ");
            $stmt->execute([
                ':full_name' => $full_name,
                ':phone' => $phone,
                ':email' => $email,
                ':birth_date' => $birth_date,
                ':gender' => $gender,
                ':biography' => $biography,
                ':contract_accepted' => $contract_accepted,
                ':id' => $_SESSION['uid']
            ]);
            $application_id = $_SESSION['uid'];

            // Удаляем старые языки
            $stmt = $pdo->prepare("DELETE FROM application_language WHERE application_id = ?");
            $stmt->execute([$application_id]);
        } else {
            // Новая запись
            $base_login = preg_replace('/[^a-z0-9]/i', '', explode('@', $email)[0]);
            $login = $base_login . '_' . rand(1000, 9999);
            while (true) {
                $stmt_check = $pdo->prepare("SELECT id FROM application WHERE login = ?");
                $stmt_check->execute([$login]);
                if (!$stmt_check->fetch()) break;
                $login .= rand(10, 99);
            }
            $password = substr(md5(uniqid()), 0, 8);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO application
                (full_name, phone, email, birth_date, gender, biography, contract_accepted, login, password_hash)
                VALUES (:full_name, :phone, :email, :birth_date, :gender, :biography, :contract_accepted, :login, :pass_hash)
            ");
            $stmt->execute([
                ':full_name' => $full_name,
                ':phone' => $phone,
                ':email' => $email,
                ':birth_date' => $birth_date,
                ':gender' => $gender,
                ':biography' => $biography,
                ':contract_accepted' => $contract_accepted,
                ':login' => $login,
                ':pass_hash' => $password_hash
            ]);
            $application_id = $pdo->lastInsertId();

            setcookie('login', $login, time() + 30*24*3600);
            setcookie('pass', $password, time() + 30*24*3600);
        }

        // Вставка языков
        $lang_map = [];
        $stmt = $pdo->query("SELECT id, name FROM language");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lang_map[$row['name']] = $row['id'];
        }
        $stmt = $pdo->prepare("INSERT INTO application_language (application_id, language_id) VALUES (?, ?)");
        foreach ($languages as $lang_name) {
            if (isset($lang_map[$lang_name])) {
                $stmt->execute([$application_id, $lang_map[$lang_name]]);
            }
        }

        $pdo->commit();

        // Удаляем куки ошибок
        $fields = ['full_name', 'phone', 'email', 'birth_date', 'gender', 'biography', 'contract_accepted', 'languages'];
        foreach ($fields as $field) {
            setcookie($field . '_error', '', 1);
        }

        setcookie('save', '1', time() + 24*3600);
        header('Location: index.php');
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        setcookie('db_error', '1', time() + 24*3600);
        header('Location: index.php');
        exit();
    }
}