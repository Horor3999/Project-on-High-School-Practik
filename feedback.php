<?php
// feedback.php

// Настройки подключения

$host = "localhost";      // или IP сервера с PostgreSQL
$port = 5432;             // порт PostgreSQL, обычно 5432
$dbname = "mytravelerdb"; // имя твоей базы
$user = "postgres"; // имя пользователя PostgreSQL
$password = "postgres";   // пароль пользователя

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Подключение успешно!";
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$success = '';
$error = '';

// Обработка отправки формы
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $message = trim($_POST["message"] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = "Пожалуйста, заполните все поля.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Введите корректный email.";
    } else {
        // Вставляем в БД
        $stmt = $pdo->prepare("INSERT INTO feedback (name, email, message) VALUES (:name, :email, :message)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message
        ]);
        $success = "Спасибо за ваш отзыв!";
    }
}

// Получаем отзывы из БД
$stmt = $pdo->query("SELECT name, email, message, created_at FROM feedback ORDER BY created_at DESC");
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Обратная связь — Мой Путешественник</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
    <h1>Мой Путешественник</h1>
    <nav>
        <a href="index.html">Главная</a>
        <a href="about.html">О себе</a>
        <a href="gallery.html">Галерея</a>
        <a href="todolist.html">ToDoList</a>
        <a href="feedback.php" aria-current="page">Обратная связь</a>
    </nav>
</header>
<main>
    <h2>Обратная связь</h2>

    <?php if ($success): ?>
        <p class="success-message"><?=htmlspecialchars($success)?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error-message"><?=htmlspecialchars($error)?></p>
    <?php endif; ?>

    <form method="post" action="feedback.php" novalidate>
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required value="<?=htmlspecialchars($_POST['name'] ?? '')?>">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">

        <label for="message">Сообщение:</label>
        <textarea id="message" name="message" rows="5" required><?=htmlspecialchars($_POST['message'] ?? '')?></textarea>

        <button type="submit">Отправить</button>
    </form>

    <section class="feedback-list">
        <h3>Отзывы пользователей</h3>
        <?php if (count($feedbacks) === 0): ?>
            <p>Пока нет отзывов.</p>
        <?php else: ?>
            <?php foreach ($feedbacks as $fb): ?>
                <div class="feedback-item">
                    <strong><?=htmlspecialchars($fb['name'])?></strong> (<?=htmlspecialchars($fb['email'])?>)
                    <em>[<?=htmlspecialchars($fb['created_at'])?>]</em>
                    <p><?=nl2br(htmlspecialchars($fb['message']))?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<footer>
    <p>© 2025 Мой Путешественник</p>
</footer>
</body>
</html>
