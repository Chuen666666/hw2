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

    <title>報名成功｜奶龍選美大賽</title>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600"
          rel="stylesheet"
          type="text/css">

    <style>
        #success {
            min-height: 100vh;
            background: url('images/register-bg.jpg') center center / cover no-repeat fixed;
            display: flex;
            align-items: center;
            padding: 80px 0;
        }

        .success-card {
            background: rgba(255, 255, 255, 0.94);
            padding: 50px;
            border-radius: 12px;
            text-align: center;
        }

        .success-card .success-icon {
            font-size: 70px;
            color: #f2545f;
            margin-bottom: 20px;
        }

        .success-card h1 {
            margin-bottom: 30px;
        }

        .success-info {
            text-align: left;
            max-width: 500px;
            margin: 30px auto;
            font-size: 16px;
        }

        .success-info p {
            color: #555;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .success-card .btn {
            margin: 8px;
            padding: 12px 28px;
            border-radius: 100px;
        }
    </style>

</head>

<body>

    <!-- =========================
         NAVIGATION
    ============================== -->
    <div class="navbar navbar-fixed-top custom-navbar" role="navigation">
        <div class="container">

            <div class="navbar-header">
                <a href="index.html" class="navbar-brand">
                    奶龍選美大賽
                </a>
            </div>

        </div>
    </div>


    <!-- =========================
         SUCCESS SECTION
    ============================== -->
    <section id="success">

        <div class="container">
            <div class="row">

                <div class="col-md-offset-2 col-md-8
                            col-sm-offset-1 col-sm-10">

                    <div class="success-card wow fadeInUp">

                        <div class="success-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>

                        <h1>報名成功！</h1>

                        <p>
                            你已成功完成奶龍選美大賽報名，
                            報名確認信已寄送至你的學校信箱。
                        </p>

                        <div class="success-info">

                            <p>
                                <strong>姓名：</strong>
                                <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p>
                                <strong>學號：</strong>
                                <?= htmlspecialchars($student_id, ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p>
                                <strong>報名組別：</strong>
                                <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>
                            </p>

                        </div>

                        <a href="<?= htmlspecialchars($pdf_link, ENT_QUOTES, 'UTF-8') ?>"
                           class="btn btn-danger"
                           target="_blank">
                            <i class="fa fa-file-pdf-o"></i>
                            查看報名 PDF
                        </a>

                        <a href="index.html"
                           class="btn btn-default">
                            <i class="fa fa-home"></i>
                            回到活動首頁
                        </a>

                    </div>

                </div>

            </div>
        </div>

    </section>


    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>

</body>

</html>