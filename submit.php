<?php

require_once('../TCPDF/tcpdf_import.php');

/*---------------- Sent Mail Start -----------------*/

/*---------------- Sent Mail End -------------------*/

/*---------------- Print PDF Start -----------------*/
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetFont('cid0jp','', 18); 
$pdf->AddPage();

$name = $_POST['name'];
$student_id = $_POST['student_id'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$grade = $_POST['grade'];
$category = $_POST['category'];

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

$html = <<<EOF
<h1>奶龍選美大賽報名表</h1>

<table border="1" cellpadding="6">
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
        <td>$email</td>
    </tr>
    <tr>
        <td>手機</td>
        <td>$phone</td>
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
/*---------------- Print PDF End -------------------*/

$pdf->writeHTML($html);
$pdf->lastPage();
$pdf->Output('order.pdf', 'I');