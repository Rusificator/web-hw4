<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Задание 4 - Анкета</title>
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
            background-color: #39e704;
            color: white;
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .nav-buttons a:hover {
            background-color: #2ecc71;
        }
    </style>
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
                    <?php foreach ($languages_from_db as $lang): ?>
                        <option value="<?= htmlspecialchars($lang) ?>" <?= in_array($lang, $values['languages'] ?? []) ? 'selected' : '' ?>><?= htmlspecialchars($lang) ?></option>
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

        <div class="nav-buttons">
            <a href="podg.html">📖 Этапы выполнения работы</a>
            <a href="view.php">📊 Просмотр сохранённых анкет</a>
        </div>
    </div>
</body>
</html>