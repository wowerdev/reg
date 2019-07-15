<?php
// Настройка подключения к бд

$auth_login = "trinity"; // Логин от базы данных аккаунтов. По умолчанию trinity
$auth_pass = "trinity"; // Пароль от базы данных аккаунтов. По умолчанию trinity
$auth_bd = "auth"; // Имя базы данных аккаунтов. По умолчанию auth
$auth_host = "127.0.0.1"; // Адрес базы данных аккаунтов. По умолчанию 127.0.0.1

// Конец настроек
?>

<?php

$authConfig = [
  "bd" => $auth_bd,
  "host" => $auth_host,
  "login" => $auth_login,
  "pass" => $auth_pass
];

$connectAuth = new mysqli($authConfig["host"], $authConfig["login"], $authConfig["pass"], $authConfig["bd"]);
$connectAuth->query("SET NAMES `utf8` COLLATE `utf8_general_ci`");

if (isset($_POST["reg"]) and !isset($_POST["test_robot"]) and isset($_POST["login"]) and isset($_POST["pass"]) and isset($_POST["pass2"]) and isset($_POST["mail"]) and $connectAuth) {
  // Обрабатываем ajax запросы
  $login = strtoupper(getSafePost($_POST["login"], $connectAuth));
  $pass = getSafePost($_POST["pass"], $connectAuth);
  $pass2 = getsafePost($_POST["pass2"], $connectAuth);
  $mail = getSafePost($_POST["mail"], $connectAuth);
  $errorMsg = false;

  if ($pass != $pass2) {
    $errorMsg = "<span class='red'>Пароли не совпадают</span>";
  } else if (isValidPassReg($pass)) {
    $errorMsg = isValidPassReg($pass);
  } else if (isValidLoginReg($login)) {
    $errorMsg = isValidLoginReg($login);
  } else {
    // Если нет ошибок, делаем запрос в бд
    $sql = "SELECT * FROM `account` WHERE `username` = '$login' LIMIT 1";
    $res = $connectAuth->query($sql);
    if ($res and $res->num_rows > 0) {
      $errorMsg = "<span class='red'>Логин занят</span>";
    } else {
      $sha_pass = strtoupper(sha1(strtoupper($login) . ":" . strtoupper($pass)));
      $sql = "INSERT INTO `account` (`username`, `sha_pass_hash`,  `email`) VALUES('$login', '$sha_pass', '$mail')";
      $res = $connectAuth->query($sql);
      $errorMsg = $connectAuth->error ? $connectAuth->error : "<span class='green'>Аккаунт $login успешно создан!</span>";
    }
  }

  echo $errorMsg;



  // Конец обработки ajax запросов
} else if (!$connectAuth->connect_errno) {

  ?>

  <!DOCTYPE html>
  <html lang="ru">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Новый проект">
    <title>Страница регистрации</title>
    <link rel="stylesheet" href="css/main.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  </head>

  <body class="body">
    <main class="main">
      <section class="reg">
        <div class="reg__center">
          <header class="reg__header">
            <h1 class="reg__register">Регистрация</h1>
          </header>
          <form action="/" method="POST" class="reg__form">
            <label class="reg__label">
              <span>Логин</span>
              <input type="text" class="reg__input" name="login" required placeholder="Введите логин">
            </label>
            <label class="reg__label">
              <span>Пароль</span>
              <input type="password" min="6" class="reg__input" name="pass" required placeholder="Введите пароль">
            </label>
            <label class="reg__label">
              <span>Повторите пароль</span>
              <input type="password" min="6" class="reg__input" name="pass2" required placeholder="Повторите пароль">
            </label>
            <label class="reg__label">
              <span>Email</span>
              <input type="email" min="6" class="reg__input" name="mail" required placeholder="Введите ваш email">
            </label>
            <input type="checkbox" class="none" name="test_robot">
            <input type="checkbox" checked class="none" name="reg">
            <button class="reg__btn">Зарегистрироваться</button>
          </form>
          <footer class="reg__footer">
            <p class="reg__realm">SET REALMLIST TEST.WOW.RU</p>
          </footer>
        </div>
      </section>
    </main>
    <script src="js/root.js"></script>
  </body>

  </html>
<?php } else {
  echo "Ошибка соединения с бд $connectAuth->error";
}

function isValidLoginReg($str)
{
  if (strlen($str) > 32 or strlen($str) < 4) {
    return "<span class='red'>Логин должен быть от 4 до 32 символов</span>";
  } else if (!ctype_alnum($str)) {
    return "<span class='red'>Логин должен состоять из англ букв и цифр</span>";
  } else {
    return false;
  }
}

function isValidPassReg($str)
{
  if (strlen($str) > 32 or strlen($str) < 4) {
    return "<span class='red'>Пароль должен быть от 4 до 32 символов</span>";
  } else if (!ctype_alnum($str)) {
    return "<span class='red'>Пароль должен состоять из англ букв и цифр</span>";
  } else {
    return false;
  }
}

function getSafePost($str, $link)
{
  $str = trim($str);
  $str = strip_tags($str);
  $str = addslashes($str);
  $str = htmlspecialchars($str);
  $str = mysqli_escape_string($link, $str);
  return $str;
}

?>