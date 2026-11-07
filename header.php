<h1>Student Grading System</h1>

    <nav>
        <a href="index.php">Records</a> |
        <a href="add.php">Add Result</a> |
        <form method="post" action="logout.php" style="display:inline">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit">Logout (<?php echo htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>)</button>
        </form> |
    </nav>

    <hr>