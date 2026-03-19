<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Задание 4 - Анкета</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Анкета</h1>

        <!-- Вывод сообщений (ошибки, успех) -->
        <?php if (!empty($messages)): ?>
            <div class="messages">
                <?php foreach ($messages as $msg): ?>
                    <?= $msg ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php">
            <!-- ФИО -->
            <div class="form-group">
                <label for="full_name">ФИО:</label>
                <input type="text" id="full_name" name="full_name"
                    value="<?= htmlspecialchars($values['full_name'] ?? '') ?>"
                    <?= !empty($errors['full_name']) ? 'class="error"' : '' ?>>
                <?php if (!empty($errors['full_name'])): ?>
                    <span class="field-error">Некорректное ФИО</span>
                <?php endif; ?>
            </div>

            <!-- Телефон -->
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone"
                    value="<?= htmlspecialchars($values['phone'] ?? '') ?>"
                    <?= !empty($errors['phone']) ? 'class="error"' : '' ?>>
                <?php if (!empty($errors['phone'])): ?>
                    <span class="field-error">Некорректный телефон</span>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email"
                    value="<?= htmlspecialchars($values['email'] ?? '') ?>"
                    <?= !empty($errors['email']) ? 'class="error"' : '' ?>>
                <?php if (!empty($errors['email'])): ?>
                    <span class="field-error">Некорректный email</span>
                <?php endif; ?>
            </div>

            <!-- Дата рождения -->
            <div class="form-group">
                <label for="birth_date">Дата рождения:</label>
                <input type="date" id="birth_date" name="birth_date"
                    value="<?= htmlspecialchars($values['birth_date'] ?? '') ?>"
                    <?= !empty($errors['birth_date']) ? 'class="error"' : '' ?>>
                <?php if (!empty($errors['birth_date'])): ?>
                    <span class="field-error">Некорректная дата</span>
                <?php endif; ?>
            </div>

            <!-- Пол -->
            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="male"
                            <?= ($values['gender'] ?? '') === 'male' ? 'checked' : '' ?>
                            <?= !empty($errors['gender']) ? 'class="error"' : '' ?>> Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="female"
                            <?= ($values['gender'] ?? '') === 'female' ? 'checked' : '' ?>
                            <?= !empty($errors['gender']) ? 'class="error"' : '' ?>> Женский
                    </label>
                </div>
                <?php if (!empty($errors['gender'])): ?>
                    <span class="field-error">Выберите пол</span>
                <?php endif; ?>
            </div>

            <!-- Любимые языки программирования -->
            <div class="form-group">
                <label for="languages">Любимые языки программирования (выберите один или несколько):</label>
                <select id="languages" name="languages[]" multiple size="6"
                    <?= !empty($errors['languages']) ? 'class="error"' : '' ?>>
                    <?php
                    // Используем глобальную переменную $allowed_languages, определённую в index.php
                    $langs_from_db = $allowed_languages ?? [];
                    try {
                        if (function_exists('getDB')) {
                            $pdo = getDB();
                            $stmt = $pdo->query("SELECT name FROM language ORDER BY name");
                            $langs_from_db = [];
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $langs_from_db[] = $row['name'];
                            }
                        }
                    } catch (Exception $e) {
                        $langs_from_db = $allowed_languages ?? [];
                    }
                    foreach ($langs_from_db as $lang):
                        $selected = in_array($lang, $values['languages'] ?? []) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($lang) ?>" <?= $selected ?>><?= htmlspecialchars($lang) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['languages'])): ?>
                    <span class="field-error">Выберите хотя бы один допустимый язык</span>
                <?php endif; ?>
            </div>

            <!-- Биография -->
            <div class="form-group">
                <label for="biography">Биография:</label>
                <textarea id="biography" name="biography" rows="6"
                    <?= !empty($errors['biography']) ? 'class="error"' : '' ?>><?= htmlspecialchars($values['biography'] ?? '') ?></textarea>
                <?php if (!empty($errors['biography'])): ?>
                    <span class="field-error">Биография слишком длинная</span>
                <?php endif; ?>
            </div>

            <!-- Чекбокс согласия -->
            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="contract_accepted" value="1"
                        <?= !empty($values['contract_accepted']) ? 'checked' : '' ?>
                        <?= !empty($errors['contract_accepted']) ? 'class="error"' : '' ?>>
                    Я ознакомлен(а) с контрактом
                </label>
                <?php if (!empty($errors['contract_accepted'])): ?>
                    <span class="field-error">Необходимо подтвердить согласие</span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <button type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</body>
</html>