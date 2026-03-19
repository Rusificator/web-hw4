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
                    // Использую глобальную переменную $allowed_languages, определённую в index.php
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


        <section class="task">

                <div class="description">
                    <p> Для удобного просмотра всех сохранённых анкет создана отдельная страница: <a href="view.php" target="_blank">Просмотр сохранённых записей</a>.</p>
                </div>

            <h2>Этапы выполнения задания №4</h2>

            
            <div class="subtask">
                <h3>1. Подготовка рабочего каталога на сервере</h3>
                <div class="description">
                    <p>Через SSH выполнен вход на сервер <code>kubsu-dev.ru</code>, создан каталог <code>~/www/hw4</code>.</p>
                </div>
                <div class="screenshot">
                    <img src="1.PNG" alt="Создание каталога hw4">
                    <p class="caption">Скриншот 1: Подключение по SSH и создание каталога</p>
                </div>
            </div>

            
            <div class="subtask">
                <h3>2. Подготовка локальных файлов</h3>
                <div class="description">
                    <p>На локальном компьютере в папке <code>laba4</code> созданы файлы: <code>index.php</code>, <code>form.php</code>, <code>style.css</code> и скриншоты для отчёта.</p>
                </div>
                <div class="screenshot">
                    <img src="2.png" alt="Локальные файлы">
                    <p class="caption">Скриншот 2: Содержимое локальной папки</p>
                </div>
            </div>

            
            <div class="subtask">
                <h3>3. Инициализация Git и отправка на GitHub</h3>
                <div class="command">
                    <strong>Команды:</strong>
                    <pre><code>git init
git add .
git commit -m "first commit"
git branch -M main
git remote add origin git@github.com:Rusificator/web-hw4.git
git push -u origin main</code></pre>
                </div>
                <div class="description">
                    <p>Локальный репозиторий создан, выполнена отправка файлов на GitHub.</p>
                </div>
                <div class="screenshot">
                    <img src="3.png" alt="Git push">
                    <p class="caption">Скриншот 3: Отправка на GitHub</p>
                </div>
            </div>

            
            <div class="subtask">
                <h3>4. Клонирование репозитория на сервер</h3>
                <div class="command">
                    <strong>Команда на сервере:</strong>
                    <pre><code>git clone git@github.com:Rusificator/web-hw4.git
cp -r ~/web-hw4/* ~/www/hw4/</code></pre>
                </div>
                <div class="description">
                    <p>Репозиторий склонирован в домашнюю директорию, файлы скопированы в веб-доступный каталог.</p>
                </div>
                <div class="screenshot">
                    <img src="4.png" alt="Клонирование на сервере">
                    <p class="caption">Скриншот 4: Клонирование и копирование</p>
                </div>
            </div>

            
            <div class="subtask">
                <h3>5. Обновление файлов на сервере</h3>
                <div class="command">
                    <strong>Команды:</strong>
                    <pre><code>git pull
cp -r ~/web-hw4/* ~/www/hw4/</code></pre>
                </div>
                <div class="description">
                    <p>При повторных изменениях выполнялся <code>git pull</code> и повторное копирование в <code>~/www/hw4</code>.</p>
                </div>
                <div class="screenshot">
                    <img src="5.png" alt="Git pull на сервере">
                    <p class="caption">Скриншот 5: Обновление на сервере</p>
                </div>
            </div>
        </section>

        
        <section class="task">
            <h2>Что такое Cookies и чем задание №4 отличается от №3</h2>
            <div class="description">
                <h3>🍪 Cookies (куки)</h3>
                <p><strong>Определение:</strong> Cookies — это небольшие фрагменты данных, которые сервер отправляет браузеру и которые сохраняются на стороне клиента. При каждом последующем запросе к этому серверу браузер автоматически отправляет соответствующие куки обратно.</p>
                <p><strong>Преимущества использования в задании:</strong></p>
                <ul>
                    <li><strong>Сохранение состояния:</strong> позволяют «запоминать» пользователя между сеансами (например, предзаполнение формы ранее введёнными данными).</li>
                    <li><strong>Передача ошибок валидации:</strong> при редиректе после POST можно сохранить информацию об ошибках и значениях полей в куках, а на GET-странице извлечь их и отобразить подсветку и сообщения.</li>
                    <li><strong>Автоматическое удаление:</strong> установив короткое время жизни для кук с ошибками, они исчезают после отображения, не засоряя браузер.</li>
                    <li><strong>Долговременное хранение:</strong> для успешно введённых данных можно установить куки на год, чтобы при следующем визите форма уже была заполнена.</li>
                </ul>

                <h3>🔍 Отличия от лабораторной работы №3</h3>
                <table style="width:100%; border-collapse: collapse; margin-top:15px;">
                    <tr style="background-color:#f0f0f0;">
                        <th style="padding:8px; border:1px solid #ddd;">Аспект</th>
                        <th style="padding:8px; border:1px solid #ddd;">Задание №3</th>
                        <th style="padding:8px; border:1px solid #ddd;">Задание №4</th>
                    </tr>
                    <tr>
                        <td style="padding:8px; border:1px solid #ddd;"><strong>Валидация</strong></td>
                        <td style="padding:8px; border:1px solid #ddd;">Выполнялась на сервере, но при ошибках форма просто перезагружалась с заполненными полями (через переменные PHP).</td>
                        <td style="padding:8px; border:1px solid #ddd;">При ошибках происходит редирект на GET, а информация об ошибках и значениях передаётся через <strong>Cookies</strong>.</td>
                    </tr>
                    <tr>
                        <td style="padding:8px; border:1px solid #ddd;"><strong>Подсветка ошибок</strong></td>
                        <td style="padding:8px; border:1px solid #ddd;">Не требовалась, только общие сообщения.</td>
                        <td style="padding:8px; border:1px solid #ddd;">Поля с ошибками подсвечиваются красным (класс <code>.error</code>), рядом выводятся конкретные сообщения.</td>
                    </tr>
                    <tr>
                        <td style="padding:8px; border:1px solid #ddd;"><strong>Сохранение значений</strong></td>
                        <td style="padding:8px; border:1px solid #ddd;">Только в БД, форма после успеха очищалась.</td>
                        <td style="padding:8px; border:1px solid #ddd;">После успешной отправки значения сохраняются в Cookies на год, и при следующем заходе форма автоматически заполняется этими данными.</td>
                    </tr>
                    <tr>
                        <td style="padding:8px; border:1px solid #ddd;"><strong>Обработка ошибок</strong></td>
                        <td style="padding:8px; border:1px solid #ddd;">Ошибки хранились в массиве <code>$errors</code> и передавались непосредственно в форму.</td>
                        <td style="padding:8px; border:1px solid #ddd;">Ошибки сохраняются в куки (<code>full_name_error</code> и т.д.), при GET-запросе они считываются и сразу удаляются.</td>
                    </tr>
                  
                </table>
                <p style="margin-top:15px;">Таким образом, задание №4 углубляет понимание работы с Cookies, учит правильно передавать состояние между запросами и улучшает пользовательский интерфейс за счёт подсветки ошибок и сохранения введённых данных.</p>
            </div>
        </section>




    </div>
</body>
</html>