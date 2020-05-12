<?php

/**
 * Задача 6. Реализовать вход администратора с использованием
 * HTTP-авторизации для просмотра и удаления результатов.
 **/

// Пример HTTP-аутентификации.
// PHP хранит логин и пароль в суперглобальном массиве $_SERVER.
// Подробнее см. стр. 26 и 99 в учебном пособии Веб-программирование и веб-сервисы.
if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    md5($_SERVER['PHP_AUTH_PW']) != md5('admin')) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
}

// Инициализируем переменные для подключения к базе данных.
$db_user = 'u16671';   // Логин БД
$db_pass = '3137204';  // Пароль БД

// Подключаемся к базе данных на сервере.
$db = new PDO('mysql:host=localhost;dbname=u16671', $db_user, $db_pass, array(
    PDO::ATTR_PERSISTENT => true
));

// Если метод был POST, значит мы нажали на кнопку удаления.
// Пробуем удалить запись из базы данных.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $db->prepare('DELETE FROM web6 WHERE login = ?');
        $stmt->execute(array(
            $_POST['remove']
        ));
    } catch (PDOException $e) {
        echo 'Ошибка: ' . $e->getMessage();
        exit();
    }
}

try {
    $stmt = $db->query('SELECT * FROM web6');
    ?>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Админ панель | Задание 6</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <form action="" method="post">
        <div class="table-container">
          <table class="table is-hoverable is-fullwidth">
              <thead>
              <tr>
                  <th>Логин</th>
                  <th>Пароль</th>
                  <th>Имя</th>
                  <th>Email</th>
                  <th>Год гождения</th>
                  <th>Пол</th>
                  <th>Количество конечностей</th>
                  <th>Сверхспособности</th>
                  <th>Биография</th>
                  <th>Удалить</th>
              </tr>
              </thead>
              <tbody>
              <?php
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  print('<tr>');
                  foreach ($row as $cell) {
                      print('<td>' . $cell . '</td>');
                  }
                  print('<td><button class="button is-info is-small is-danger is-light" name="remove" type="submit" value="' . $row['login'] . '">x</button></td>');
                  print('</tr>');
              }
              ?>
              </tbody>
          </table>
        </div>
    </form>
    </body>
    <?php
} catch (PDOException $e) {
    echo 'Ошибка: ' . $e->getMessage();
    exit();
}
