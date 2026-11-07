<style>
    label {
        display: inline-block;
        width: 200px;
    }
</style>

<form method="post" action="">

    <label for="student_name">Student Name:</label>
    <input type="text" id="student_name" name="student_name" value="<?php echo $student_name; ?>"><br><br>
    
    <label for="matric_number">Matric Number:</label>
    <input type="text" id="matric_number" name="matric_number" value="<?php echo $matric_number; ?>"><br><br>

    <label for="subject_name">Subject:</label>
    <input type="text" id="subject_name" name="subject_name" value="<?php echo $subject_name; ?>"><br>

    <label for="subject_code">Subject Code:</label>
    <input type="text" id="subject_code" name="subject_code" value="<?php echo $subject_code; ?>"><br>

    <label for="test_1">Test 1 (20%):</label>
    <input type="number" id="test_1" name="test_1" value="<?php echo $test_1; ?>"><br>

    <label for="test_2">Test 2 (30%):</label>
    <input type="number" id="test_2" name="test_2" value="<?php echo $test_2; ?>" required> out of 30<br>

    <label for="final_examination">Final Exam (50%):</label>
    <input type="number" id="final_examination" name="final_examination" value="<?php echo $final_examination; ?>" required> out of 50<br>

    <input type="submit" name="submit" value="Submit">

</form>