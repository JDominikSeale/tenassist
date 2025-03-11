<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
class userInput {
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    private $submission_ID;

    function __construct($first_name, $last_name, $email, $phone){
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->submission_ID = null;
    }

    function id(){
        return $this->submission_ID;
    }
    
    function uploadData(){
        include ('config.php');
        $conn = new mysqli(DB_HOST_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($conn->connect_error) {
            return [False, "Server Error"];
        }
        $sql = "INSERT INTO customer (first_name, last_name, email, phone) VALUES ('$this->first_name', '$this->last_name', '$this->email', '$this->phone')";
        if ($conn->query($sql) === TRUE) {
            $this->submission_ID = $conn->insert_id;
            return [True, "Success"];
        } else{
            return [False, "Duplicate"];
        }
    }
}

class fileUpload{
    public $file_name;
    public $file_ext;
    public $file_size;
    public $file_tmp_name;

    function __construct($file_name, $file_ext, $file_size, $file_tmp_name){
        $this->file_name = $file_name;
        $this->file_ext = $file_ext;
        $this->file_size = $file_size;
        $this->file_tmp_name = $file_tmp_name;
    }

    function sendToDir($submission_ID){
        $target_dir = ".../secure_uploads/tenassist/";
        $newFileName = $submission_ID . "_" . $this->file_name;
        $target_file = $target_dir . basename($newFileName);
        move_uploaded_file($this->file_tmp_name, $target_file);
    }

}


if(isset($_POST['form_submit_1'])){
    $rawInput = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone']
    ];
    $cleanInput = array_map('htmlspecialchars', $rawInput);
    $user = new userInput($cleanInput['first_name'], $cleanInput['last_name'], $cleanInput['email'], $cleanInput['phone']);
    $userReturn = $user->uploadData();
    if($userReturn[0] == False){
        header('Location: pages/home.php');
        exit();
    } elseif ($userReturn[0] == True){
        header('Location: pages/thankyou.php');
        exit();
    }

} elseif(isset($_POST['upload_documents'])){
    if(!empty($_FILES["file"])){
        $allowedExt = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png');
        $file = $_FILES["file"];
        $file_ext = explode('.', $file['name']);
        $file_ext = strtolower(end($file_ext));
        if(!in_array($file_ext, $allowedExt)){
            echo "File type not allowed";
        } else{
            $fileUpload = new fileUpload($file['name'], $file_ext, $file['size'], $file['tmp_name']);
            $fileUpload->sendToDir($user->id());
            header('Location: pages/thankyou.php');
            exit();
        }        
    };
} else{
    header('Location: pages/home.php');
    exit();
}

?>