<?php

class Model {
    private $server = "127.0.0.1";
    private $username = "root";
    private $password;
    private $db = "oop_crud";
    private $conn;


    public function __construct(){
        try {

            $this->conn = new mysqli($this->server, $this->username, $this->password, $this->db);

        } catch (Exception $e) {

            echo "connection failed " .$e->getMessage();
        }
    }


    public function insert(){
        
        if (isset($_POST['submit'])) {

            if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['address'])) {

                if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['mobile']) && !empty($_POST['address'])) {

                    $name = $_POST['name'];
                    $email = $_POST['email'];
                    $mobile = $_POST['mobile'];
                    $address = $_POST['address'];

                    $query = "INSERT INTO records (name, email, mobile, address) VALUES ('$name', '$email', '$mobile', '$address')";

                    if ($sql = $this->conn->query($query)) {
                        echo "<script>alert('records added successfully');</script>";
                        echo "<script>window.location.href = 'records.php';</script>";
                    }else {
                        echo "<script>alert('failed');</script>";
                        echo "<script>window.location.href = 'index.php';</script>";
                    }
                }else {
                    echo "<script>alert('empty');</script>";
                    echo "<script>window.location.href = 'index.php';</script>";
                }
            }
        }
    }


    public function fetch() {
        $adta = null;

        $query = "SELECT * FROM records";
        $sql = $this->conn->query($query);

        if ($sql ==  true) {
            while ($row = mysqli_fetch_assoc($sql)) {
                $data[] = $row;
            }
        }
        return $data;

    }


    public function delete($id) {

        $query = "DELETE FROM records WHERE id = '$id'";
        $sql = $this->conn->query($query);

        if ($sql == true) {
            return true;
        }else {
            return false;
        }

    }


    public function fetch_single($id) {

        $data = null;

        $query = "SELECT * FROM records WHERE id = '$id'";
        $sql = $this->conn->query($query);

        if ($sql == true) {
            while ($row = $sql->fetch_assoc()) {
                $data = $row;
            }
        }
        return $data;
    }


    public function edit($id) {

        $data = null;

        $query = "SELECT * FROM records WHERE id = '$id'";
        $sql = $this->conn->query($query);

        if ($sql == true) {
            while ($row = $sql->fetch_assoc()) {
                $data = $row;
            }
        }
        return $data;
    }


    public function update($data) {
        $query = "UPDATE records SET name='$data[name]', email='$data[email]', mobile='$data[mobile]', address='$data[address]' WHERE id = '$data[id]'";
        $sql = $this->conn->query($query);

        if ($sql == true) {
            return true;
        }else {
            return false;
        }
    }

}





?>