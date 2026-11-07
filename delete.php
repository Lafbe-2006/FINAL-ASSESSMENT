<?php
require_once __DIR__ . '/auth.php';
require_login();

include "dp.php";

if ($_SERVER ['REQUEST_METHOD'] ==='POST') {

    $id = $_POST['delete'];

    $sql = $conn->prepare("DELETE FROM results WHERE id = ?");
    $sql->bind_param("i", $id);

    if ($sql->execute()) {
        header("Location: index.php");
        exit;
    }

    exit("Error deleting result: " . $sql->error);
}

?>