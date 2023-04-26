<?php
 
// Настройки подключения к базе данных
$servername = "localhost";
$username = "u52986";
$password = "6966464";
$dbname = "u52986";
 
// Создание подключения
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
// Получение данных из формы
$name = $_POST["name"];
$email = $_POST["email"];
$year = $_POST["year"];
$sex = $_POST["sex"];
$legs = $_POST["legs"];
$powers = $_POST["powers"];
$bio = $_POST["bio"];
$agree = $_POST["agree"] == "yes";
 
// Валидация данных
$errors = [];
 
if (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $name)) {
    $errors[] = "Имя содержит недопустимые символы.";
}
 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Неверный формат e-mail.";
}
 
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    die();
}
 
// Сохранение данных в базе данных
try {
    $stmt = $db->prepare("INSERT INTO users (name, email, year, sex, legs, bio, agree) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $year, $sex, $legs, $bio, $agree]);
 
    $user_id = $db->lastInsertId();
 
    $stmt = $db->prepare("SELECT id FROM powers WHERE power_name = ?");
    foreach ($powers as $power) {
        $stmt->execute([$power]);
        $power_id = $stmt->fetchColumn();
 
        $stmt2 = $db->prepare("INSERT INTO user_powers (user_id, power_id) VALUES (?, ?)");
        $stmt2->execute([$user_id, $power_id]);
    }
 
    echo "Данные успешно сохранены.";
 } 
catch (PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}
