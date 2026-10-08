<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('../TCPDF/tcpdf_import.php');
require_once('../TCPDF/tcpdf_barcodes_2d.php');
require_once('config.php');
require_once('vendor/autoload.php');


// ---------------- Database Connection ----------------

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


// ---------------- Receive Form Data ----------------

$name = $_POST['name'];
$student_id = $_POST['student_id'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$grade = $_POST['grade'];
$category = $_POST['category'];


// ---------------- Category Conversion ----------------

$category_map = [
    'digital' => '電繪',
    'hand' => '手繪',
    'doll' => '玩偶',
    '3dprint' => '3D 列印'
];

$category_chinese = [];

foreach ($category as $item) {
    if (isset($category_map[$item])) {
        $category_chinese[] = $category_map[$item];
    }
}

$category_text = implode('、', $category_chinese);


// ---------------- PDF Information ----------------

$pdf_filename = 'registration_' . $student_id . '.pdf';

$pdf_path = __DIR__ . '/pdf/' . $pdf_filename;

$base_url = 'http://140.138.155.243/~s1151538/HW2';

$pdf_url = $base_url . '/pdf/' . $pdf_filename;


// ---------------- Save To Database ----------------

$sql = "INSERT INTO {$registration_table}
        (student_id, name, email, phone, grade, category, pdf_filename)
        VALUES
        (:student_id, :name, :email, :phone, :grade, :category, :pdf_filename)
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            email = VALUES(email),
            phone = VALUES(phone),
            grade = VALUES(grade),
            category = VALUES(category),
            pdf_filename = VALUES(pdf_filename)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':student_id' => $student_id,
    ':name' => $name,
    ':email' => $email,
    ':phone' => $phone,
    ':grade' => $grade,
    ':category' => $category_text,
    ':pdf_filename' => $pdf_filename
]);


// ---------------- Generate QR Code PNG In Memory ----------------

$barcode = new TCPDF2DBarcode(
    $pdf_url,
    'QRCODE,H'
);

$qr_png = $barcode->getBarcodePngData(6, 6);


// ---------------- Print PDF Start -----------------

$pdf = new TCPDF(
    PDF_PAGE_ORIENTATION,
    PDF_UNIT,
    PDF_PAGE_FORMAT,
    true,
    'UTF-8',
    false
);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetFont('cid0jp', '', 18);

$pdf->AddPage();


$html = <<<EOF

<style>

table {
    border-collapse: collapse;
}

td {
    padding: 8px;
}

.title {
    font-size: 20px;
    font-weight: bold;
    text-align: center;
}

</style>


<table border="1" cellpadding="6">

    <tr>
        <td colspan="2" class="title">
            奶龍選美大賽報名表
        </td>
    </tr>

    <tr>
        <td>姓名</td>
        <td>$name</td>
    </tr>

    <tr>
        <td>學號</td>
        <td>$student_id</td>
    </tr>

    <tr>
        <td>Email</td>
        <td>
            <a href="mailto:$email">$email</a>
        </td>
    </tr>

    <tr>
        <td>手機</td>
        <td>
            <a href="tel:$phone">$phone</a>
        </td>
    </tr>

    <tr>
        <td>年級</td>
        <td>大{$grade}</td>
    </tr>

    <tr>
        <td>報名組別</td>
        <td>$category_text</td>
    </tr>

</table>

EOF;


$pdf->writeHTML($html);


// ---------------- QR Code In PDF ----------------

$pdf->Image('@' . $qr_png, 80, 160, 50, 50, 'PNG');

$pdf->lastPage();

$pdf->Output($pdf_path, 'F');

// ---------------- Print PDF End -------------------


// ---------------- Sent Mail Start -----------------

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = 'smtp.office365.com';

    $mail->SMTPAuth = true;

    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;

    $mail->CharSet = 'UTF-8';


    // FROM
    $mail->setFrom($smtp_username, '奶龍選美大賽');


    // TO
    $mail->addAddress($email, $name);


    // HTML Email
    $mail->isHTML(true);

    $mail->Subject = '奶龍選美大賽報名成功';


    $mail->Body = "

        <h2>奶龍選美大賽報名成功</h2>

        <p>姓名：{$name}</p>

        <p>學號：{$student_id}</p>

        <p>學校 Email：{$email}</p>

        <p>手機：{$phone}</p>

        <p>年級：大{$grade}</p>

        <p>報名組別：{$category_text}</p>

        <p>
            QR Code 已附於本信附件。
            掃描後可直接開啟您的報名 PDF。
        </p>

    ";


    // QR Code PNG Attachment
    $mail->addStringAttachment(
        $qr_png,
        '奶龍選美大賽_QRCode.png',
        'base64',
        'image/png'
    );


    $mail->send();

} catch (Exception $e) {
    die('寄信失敗：' . $mail->ErrorInfo);
}

// ---------------- Sent Mail End -------------------


// ---------------- Redirect ------------------------

header(
    "Location: success.php?student_id=" .
    urlencode($student_id)
);

exit;