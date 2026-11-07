<?php
require_once __DIR__ . '/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Assessment - Student Grading</title>
</head>
<body>
<?php
include 'dp.php';

//?? = fallback, maksudnya, kalau output GET takdak, akan pilih kosong.
$search = $_GET['search'] ?? '';

$like = "%" . $search . "%";

$sql = $conn->prepare("SELECT * FROM results WHERE
student_name LIKE ? OR
matric_number LIKE ? OR
subject_name LIKE ? OR
subject_code LIKE ?");
$sql->bind_param("ssss", $like, $like, $like, $like);
$sql->execute();
$result = $sql->get_result();

include 'header.php';
?>

    <h2>Student Records</h2>

    <a href="add.php">Add Result</a>
    <br><br>
    <form method="get">
    <input type="text" name="search" placeholder="Search by name or matric_no"
    value="<?php echo $search ?>">
    <input type="submit" value="Search">
    <a href="index.php">Show all</a>
    </form>

    <br><br><br>

    <table border="1">
        <tr>
            <th>No.</th>
            <th>Student Name</th>
            <th>Matric No.</th>
            <th>Subject</th>
            <th>Subject Code</th>
            <th>Test 1 (20%)</th>
            <th>Test 2 (30%)</th>
            <th>Final (50%)</th>
            <th>Total</th>
            <th>Grade</th>
            <th>Actions</th>
        </tr>

        <?php
        $count = 1;

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $row['student_name'] . "</td>";
            echo "<td>" . $row['matric_number'] . "</td>";
            echo "<td>" . $row['subject_name'] . "</td>";
            echo "<td>" . $row['subject_code'] . "</td>";
            echo "<td>" . $row['test_1'] . "</td>";
            echo "<td>" . $row['test_2'] . "</td>";
            echo "<td>" . $row['final_examination'] . "</td>";
            echo "<td>" . $row['total_marks'] . "</td>";
            echo "<td>" . $row['final_grade'] . "</td>";
            echo "<td>";
            
            // Action Buttons: View, Edit, and Delete
            echo "<button><a href='edit.php?id=" . $row['id'] . "'>Edit </a></button>";
            echo "<form method='post' action='delete.php' onsubmit='return confirm(\"Delete this?\")'>";
            echo "<input type='hidden' name='delete' value='" . $row['id'] . "'>";
            echo "<input type='submit' value='Delete'>";
            echo "</form>";
            echo "</tr>";
        }

        ?>
    </table>
</body>
</html>

<?php
if ($result->num_rows == 0) {
    if ($search != "")
        echo "<h3 style='color:red'>No record match your search!!!</h3>";
    else
        echo "<h3 style='color:grey'>No records found.</h3>";
}

include 'footer.php';
?>