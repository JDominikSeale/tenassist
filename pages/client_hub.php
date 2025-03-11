<?php 
session_start();
$user = $_SESSION['user'];
if ($user == null) {
    header('Location: login.php');
    exit();
};
include_once '../server.php';



?>

<html>
    <head>

    </head>
    <body>
        <h1>Your clien hub page</h1>
        <a href="logout.php">Logout</a>
        <div>
            
        </div>
    </body>
</html>