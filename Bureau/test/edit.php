<?php

    //Include database
    include 'customers.php';
    $customerObj = new Customers();

    //Edit customer record
    if (isset($_GET['editId']) && !empty($_GET['editId'])) {
        $editId = $_GET['editId'];
         
        $customer = $customerObj->displayRecordById($editId);
    }


    //update record in customer record
    if (isset($_POST['update'])) {
        $customerObj->updateRecord($_POST);
    }

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
    <title>Php Crud Poo Mysql</title>
  </head>
  <body>
    <div class="card text-center"style="padding: 15px;">
        <h3>PHP OOP CRUD MYSQL TEST</h3>
    </div><br>

    <div class="container">
        <form action="edit.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="uname" class="form-control" value="<?php if(isset($customer)){ echo $customer['name']; }?>" placeholder="Enter name" required="">
            </div>
            <div class="form-group">
                <label for="email">Email address:</label>
                <input type="email" name="uemail" class="form-control" value="<?php if(isset($customer)){ echo $customer['email']; }?>" placeholder="Enter email" required="">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="upusername" class="form-control" value="<?php if(isset($customer)){ echo $customer['username']; }?>" placeholder="Enter username" required="">
            </div>
            <div class="form-group">
                <input type="hidden" name="id" value="<?php if(isset($customer)){ echo $customer['id']; }?>">
                <input type="submit" value="Update" class="btn btn-primary" name="update" style="float:right;">
            </div>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>