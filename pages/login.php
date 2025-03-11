<?php
session_start();
include_once '../server.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $serverInstance = new serverCon();
        $conn = $serverInstance->conn;
        $sql = "SELECT
                    id
                FROM 
                    users
                WHERE
                    username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0){
            $_SESSION['user'] = $result->fetch_assoc()['id'];
            header('Location: /pages/clinet_hub.php');
        } else {
            echo "Invalid username or password";
        }
    }
}

?>



<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <h1>Login</h1>
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            <input type="submit" value="Login">
        </form>
    </body>
</html>