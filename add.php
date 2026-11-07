<?php
require_once __DIR__ . '/auth.php';
require_login();

include 'header.php'; 
include 'dp.php';

$student_name = '';
$matric_number = '';
$subject_name = '';
$subject_code = '';
$test_1 = '';
$test_2 = '';
$final_examination = '';


if (isset($_POST['submit'])) {
    $student_name = $_POST['student_name'];
    $matric_number = $_POST['matric_number'];
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];
    $test_1 = $_POST['test_1'];
    $test_2 = $_POST['test_2'];
    $final_examination = $_POST['final_examination'];
    
    $total_marks = $test_1 + $test_2 + $final_examination;

    // 80-100 A
    // 65-79 B
    // 50-64 C
    // 40-49 D
    // 0-39 E
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

    // 1. Ensure columns and values count match (9 columns for 9 values)
    $sql = $conn->prepare("INSERT INTO results 
        (student_name, matric_number, subject_name, subject_code, test_1, test_2, Final_examination, total_marks, final_grade) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"); 

    if ($sql) {
        // 2. Bind the variables to the parameters (s = string, i = integer)
        $sql->bind_param("ssssiiiis", 
            $student_name, 
            $matric_number, 
            $subject_name, 
            $subject_code, 
            $test_1, 
            $test_2, 
            $final_examination, 
            $total_marks, 
            $grade
        );

        if ($sql->execute()) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error executing query: " . $sql->error;
        }

        $sql->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }
}
?>

<h2>Add Result</h2>

<?php
include 'footer.php';
include 'form.php';
?>