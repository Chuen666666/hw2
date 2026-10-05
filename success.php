<?php

require_once('config.php');

$student_id = $_GET['student_id'] ?? '';

if ($student_id === '') {
    die('缺少學號資料');
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$sql = "SELECT *
        FROM registrations
        WHERE student_id = :student_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':student_id' => $student_id
]);

$registration = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$registration) {
    die('找不到報名資料');
}

$name = $registration['name'];
$category = $registration['category'];
$pdf_filename = $registration['pdf_filename'];

$pdf_link = 'pdf/' . $pdf_filename;

?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>報名成功</title>
</head>

<body>

    <h1>報名成功</h1>

    <p>姓名：<?= htmlspecialchars($name) ?></p>

    <p>學號：<?= htmlspecialchars($student_id) ?></p>

    <p>報名組別：<?= htmlspecialchars($category) ?></p>

    <p>
        <a href="<?= htmlspecialchars($pdf_link) ?>" target="_blank">
            查看報名 PDF
        </a>
    </p>

    <p>
        <a href="index.html">
            回到報名首頁
        </a>
    </p>

</body>
</html>