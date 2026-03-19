<?php
/**
 * Задание 4: Форма с валидацией, Cookies и сохранением в БД
 */

header('Content-Type: text/html; charset=UTF-8');

// Функция для подключения к БД
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

// Массив допустимых языков (для валидации)
$allowed_languages = [
    'Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python',
    'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'
];

// Массив допустимых значений пола
$allowed_genders = ['male', 'female'];

// Обработка GET-запроса (отображение формы)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $messages = [];
    $errors = [];
    $values = [];

    // Проверяем наличие cookies с ошибками
    $fields = ['full_name', 'phone', 'email', 'birth_date', 'gender', 'biography', 'contract_accepted', 'languages'];
    foreach ($fields as $field) {
        $errors[$field] = !empty($_COOKIE[$field . '_error']);
    }

    // Выводим сообщения об ошибках и удаляем куки
    if ($errors['full_name']) {
        setcookie('full_name_error', '', 1);
        setcookie('full_name_value', '', 1);
        $messages[] = '<div class="error-message">ФИО должно содержать только буквы и пробелы (макс. 150 символов).</div>';
    }
    if ($errors['phone']) {
        setcookie('phone_error', '', 1);
        setcookie('phone_value', '', 1);
        $messages[] = '<div class="error-message">Телефон должен содержать от 6 до 12 цифр, допускаются символы +, -, (, ), пробел.</div>';
    }
    if ($errors['email']) {
        setcookie('email_error', '', 1);
        setcookie('email_value', '', 1);
        $messages[] = '<div class="error-message">Введите корректный email.</div>';
    }
    if ($errors['birth_date']) {
        setcookie('birth_date_error', '', 1);
        setcookie('birth_date_value', '', 1);
        $messages[] = '<div class="error-message">Дата рождения должна быть в формате ГГГГ-ММ-ДД и не позже сегодняшнего дня.</div>';
    }
    if ($errors['gender']) {
        setcookie('gender_error', '', 1);
        setcookie('gender_value', '', 1);
        $messages[] = '<div class="error-message">Выберите пол.</div>';
    }
    if ($errors['biography']) {
        setcookie('biography_error', '', 1);
        setcookie('biography_value', '', 1);
        $messages[] = '<div class="error-message">Биография не должна превышать 10000 символов.</div>';
    }
    if ($errors['contract_accepted']) {
        setcookie('contract_accepted_error', '', 1);
        setcookie('contract_accepted_value', '', 1);
        $messages[] = '<div class="error-message">Необходимо подтвердить согласие.</div>';
    }
    if ($errors['languages']) {
        setcookie('languages_error', '', 1);
        setcookie('languages_value', '', 1);
        $messages[] = '<div class="error-message">Выберите хотя бы один язык программирования из списка.</div>';
    }

    // Получаем ранее введённые значения из cookies (если есть)
    foreach ($fields as $field) {
        $values[$field] = empty($_COOKIE[$field . '_value']) ? '' : $_COOKIE[$field . '_value'];
    }
    // Для полей со множественным выбором (languages) нужно разобрать значение
    if (!empty($_COOKIE['languages_value'])) {
        $values['languages'] = explode(',', $_COOKIE['languages_value']);
    } else {
        $values['languages'] = [];
    }
    // Для чекбокса contract_accepted
    $values['contract_accepted'] = !empty($_COOKIE['contract_accepted_value']) ? true : false;

    // Сообщение об успешном сохранении
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 1);
        $messages[] = '<div class="success-message">Данные успешно сохранены!</div>';
    }

    // Подключаем файл с формой
    include 'form.php';
    exit();
}

// Обработка POST-запроса (отправка формы)
else {
    $errors = false;

    // Получаем данные из POST
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birth_date = trim($_POST['birth_date'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $biography = trim($_POST['biography'] ?? '');
    $contract_accepted = isset($_POST['contract_accepted']) ? 1 : 0;
    $languages = $_POST['languages'] ?? [];

    // ---- Валидация с регулярными выражениями ----

    // ФИО: только буквы и пробелы, длина ≤150
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

    // Телефон: от 6 до 12 символов, допустимые: цифры, пробел, -, +, (, )
    if (empty($phone)) {
        setcookie('phone_error', '1', time() + 24*3600);
        $errors = true;
    } elseif (!preg_match('/^[\d\s\-\+\(\)]{6,12}$/', $phone)) {
        setcookie('phone_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('phone_value', $phone, time() + 30*24*3600);

    // Email
    if (empty($email)) {
        setcookie('email_error', '1', time() + 24*3600);
        $errors = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('email_value', $email, time() + 30*24*3600);

    // Дата рождения
    if (empty($birth_date)) {
        setcookie('birth_date_error', '1', time() + 24*3600);
        $errors = true;
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $birth_date);
        if (!$date || $date->format('Y-m-d') !== $birth_date) {
            setcookie('birth_date_error', '1', time() + 24*3600);
            $errors = true;
        } else {
            $today = new DateTime('today');
            if ($date > $today) {
                setcookie('birth_date_error', '1', time() + 24*3600);
                $errors = true;
            }
        }
    }
    setcookie('birth_date_value', $birth_date, time() + 30*24*3600);

    // Пол
    if (empty($gender)) {
        setcookie('gender_error', '1', time() + 24*3600);
        $errors = true;
    } elseif (!in_array($gender, $allowed_genders)) {
        setcookie('gender_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('gender_value', $gender, time() + 30*24*3600);

    // Биография (необязательное, но проверяем длину)
    if (strlen($biography) > 10000) {
        setcookie('biography_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('biography_value', $biography, time() + 30*24*3600);

    // Чекбокс согласия
    if (!$contract_accepted) {
        setcookie('contract_accepted_error', '1', time() + 24*3600);
        $errors = true;
    }
    setcookie('contract_accepted_value', $contract_accepted ? '1' : '0', time() + 30*24*3600);

    // Любимые языки
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
    // Сохраняем выбранные языки как строку через запятую
    setcookie('languages_value', implode(',', $languages), time() + 30*24*3600);

    // Если есть ошибки, делаем редирект на GET
    if ($errors) {
        header('Location: index.php');
        exit();
    }

    // --- Ошибок нет, сохраняем в БД ---
    try {
        $pdo = getDB();
        $pdo->beginTransaction();

        // Вставка в application
        $stmt = $pdo->prepare("
            INSERT INTO application
            (full_name, phone, email, birth_date, gender, biography, contract_accepted)
            VALUES (:full_name, :phone, :email, :birth_date, :gender, :biography, :contract_accepted)
        ");
        $stmt->execute([
            ':full_name' => $full_name,
            ':phone' => $phone,
            ':email' => $email,
            ':birth_date' => $birth_date,
            ':gender' => $gender,
            ':biography' => $biography,
            ':contract_accepted' => $contract_accepted
        ]);
        $application_id = $pdo->lastInsertId();

        // Получаем id языков
        $lang_map = [];
        $stmt = $pdo->query("SELECT id, name FROM language");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lang_map[$row['name']] = $row['id'];
        }

        // Вставка в application_language
        $stmt = $pdo->prepare("INSERT INTO application_language (application_id, language_id) VALUES (?, ?)");
        foreach ($languages as $lang) {
            if (isset($lang_map[$lang])) {
                $stmt->execute([$application_id, $lang_map[$lang]]);
            }
        }

        $pdo->commit();

        // Удаляем все куки ошибок (на всякий случай)
        $fields = ['full_name', 'phone', 'email', 'birth_date', 'gender', 'biography', 'contract_accepted', 'languages'];
        foreach ($fields as $field) {
            setcookie($field . '_error', '', 1);
        }

        // Устанавливаем куку об успешном сохранении
        setcookie('save', '1', time() + 24*3600); // на день

        // Перенаправляем на GET
        header('Location: index.php');
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        // При ошибке БД покажем сообщение (можно через куки)
        setcookie('db_error', '1', time() + 24*3600);
        header('Location: index.php');
        exit();
    }
}