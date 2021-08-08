<?php

    //Include database
    include 'customers.php';
    $customerObj = new Customers();

    //insert record in customer table
    if (isset($_POST['submit'])) {
        $customerObj->insertData($_POST);
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
        <form action="add.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" class="form-control" placeholder="Enter name" required="">
            </div>
            <div class="form-group">
                <label for="email">Email address:</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email" required="">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required="">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="text" name="password" class="form-control" placeholder="Enter password" required="">
            </div>
                <input type="submit" value="Submit" class="btn btn-primary" name="submit" style="float:right;">
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>