<?php

require_once('../TCPDF/tcpdf_import.php');
require_once('config.php');


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


// ---------------- PDF Filename ----------------

$pdf_filename = 'registration_' . $student_id . '.pdf';
$pdf_path = __DIR__ . '/pdf/' . $pdf_filename;


// ---------------- Save To Database ----------------

$sql = "INSERT INTO registrations
        (student_id, name, email, phone, grade, category, pdf_filename)
        VALUES
        (:student_id, :name, :email, :phone, :grade, :category, :pdf_filename)";

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


// ---------------- Sent Mail Start ----------------


// ---------------- Sent Mail End -------------------


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

$pdf->lastPage();

$pdf->Output($pdf_path, 'F');


// ---------------- Print PDF End -------------------