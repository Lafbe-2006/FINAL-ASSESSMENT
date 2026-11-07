<?php
require_once __DIR__ . '/auth.php';
require_login();

include 'dp.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid result ID.');
}

$sql = $conn->prepare("SELECT * FROM results WHERE id = ?");
if (!$sql) {
    exit("Error preparing query: " . $conn->error);
}
$sql->bind_param("i", $id);
$sql->execute();
$row = $sql->get_result()->fetch_assoc();
$sql->close();

if (!$row) {
    http_response_code(404);
    exit('Result not found.');
}

$student_name = $row['student_name'];
$matric_number = $row['matric_number'];
$subject_name = $row['subject_name'];
$subject_code = $row['subject_code'];
$test_1 = $row['test_1'];
$test_2 = $row['test_2'];
$final_examination = $row['final_examination'];


if (isset($_POST['submit'])) {
    $student_name = $_POST['student_name'];
    $matric_number = $_POST['matric_number'];
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];
    $test_1 = (int) $_POST['test_1'];
    $test_2 = (int) $_POST['test_2'];
    $final_examination = (int) $_POST['final_examination'];

    $total_marks = $test_1 + $test_2 + $final_examination;

    if ($total_marks >= 80)
        $grade = 'A';
    else if ($total_marks >= 65)
        $grade = 'B';
    else if ($total_marks >= 50)
        $grade = 'C';
    else if ($total_marks >= 40)
        $grade = 'D';
    else
        $grade = 'E';

    $sql_update = $conn->prepare("UPDATE results SET
        student_name = ?,
        matric_number = ?,
        subject_name = ?,
        subject_code = ?,
        test_1 = ?,
        test_2 = ?,
        final_examination = ?,
        total_marks = ?,
        final_grade = ?
        WHERE id = ?");

    if (!$sql_update) {
        exit("Error preparing query: " . $conn->error);
    }

    $sql_update->bind_param(
        "ssssissssi",
        $student_name,
        $matric_number,
        $subject_name,
        $subject_code,
        $test_1,
        $test_2,
        $final_examination,
        $total_marks,
        $grade,
        $id
    );

    if ($sql_update->execute()) {
        header("Location: index.php");
        exit;
    } else {
        exit("Error executing query: " . $sql_update->error);
    }

    $sql_update->close();
}
?>

<?php include 'header.php'; ?>

<h2>Edit Result</h2>

<?php
include 'form.php';
include 'footer.php';
?>
