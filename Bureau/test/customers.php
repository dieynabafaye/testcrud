<?php


class Customers {
    private $servername = "127.0.0.1";
    private $username = "root";
    private $password = "";
    private $database = "crud_php";
    private $conn;

    //database connection
    public function __construct() 
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);

        if (mysqli_connect_error()) {
            trigger_error("Failed to connect to Mysql: " . mysqli_connect_error());
        }else {
            return $this->conn;
        }
    }


    //insert customer data into customer table
    public function insertData() {
        $name = $this->conn->real_escape_string($_POST['name']);
        $email = $this->conn->real_escape_string($_POST['email']);
        $username = $this->conn->real_escape_string($_POST['username']);
        $password = $this->conn->real_escape_string(md5($_POST['password']));

        $query = "INSERT INTO customers(name,email,username,password) VALUES ('$name', '$email','$username','$password')";

        $sql = $this->conn->query($query);

        if ($sql == true) {
            header ("Location: index.php?msg1=insert");
        }else {
            echo "Registratio failed try again!";
        }
    }


    //fetch customer records for show listing
    public function displayData() 
    {
        $query = "SELECT * FROM customers";
        $result = $this->conn->query($query);

        if ($result->num_rows > 0) {
            $data = array();
            
            while ($row = $result->fetch_assoc()) {
                $data[]  = $row;
            }
            return $data;
        }else {
            echo "No found records";
        }
    }


    // Fetch single data for edit from customer table
    public function displayRecordById($id)
    {
        $query = "SELECT * FROM customers WHERE id = '$id'";
        $result = $this->conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        }else {
            echo "Records not found";
        }
    }


    // Update customer data into customer table
    public function updateRecord($postData) 
    {
        $name = $this->conn->real_escape_string($_POST['uname']);
        $email = $this->conn->real_escape_string($_POST['uemail']);
        $username = $this->conn->real_escape_string($_POST['upusername']);
        $id = $this->conn->real_escape_string(md5($_POST['id']));

        if (!empty($id) && !empty($postData)) {
            $query = "UPDATE customers SET name = '$name', email = '$email', username = '$username' WHERE id = '$id'";

            $sql = $this->conn->query($query);
            if ($sql == true) {
                header ('Location:index.php?msg2=update');
            }else {
                echo "Registration update failed to update try again!";
            }

        }
    }


    // Delete customer data from customer table
    public function deleteRecord($id)
    {
        $query = "DELETE FROM customers WHERE id ='$id'";
        $sql = $this->conn->query($query);

        if ($sql==true) {
            header ('Location:index.php?msg3=delete');
        }else {
            echo 'Record does not delete try again!';
        }
    }
}









?>