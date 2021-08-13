<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
    <div class="container">
        <div class="row">
            <div class="col md-12 mt-5">
                <h1 class="text-center">PHP OOP CRUD TEST</h1>
                <hr style="height: 1px;color: black;background-color: black;"> 
            </div>
        </div>
        <div class="row">
            <div class="col md-5 mx-auto">
            <?php

                include 'model.php';
                $model =  new Model();
                $id = $_REQUEST['id'];
                $row = $model->edit($id);

                  
                if (isset($_POST['update'])) {

                    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['address'])) {

                        if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['mobile']) && !empty($_POST['address'])) {

                            $data['id'] = $id;
                            $data['name'] = $_POST['name'];
                            $data['email'] = $_POST['email'];
                            $data['mobile'] = $_POST['mobile'];
                            $data['address'] = $_POST['address'];


                            $update = $model->update($data);

                            if ($update) {
                                echo "<script>alert('record update successfully');</script>";
                                echo "<script>window.location.href = 'records.php';</script>";
                            }else{
                                echo "<script>alert('empty');</script>";
                                echo "<script>window.location.href = 'records.php';</script>";
                            }

                            
                        }else {
                            echo "<script>alert('record update failed');</script>";
                            header('Location:edit.php?id=$id');
                        }
                    }
                }

            ?>


                <form action="" method="post">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" name="name" value="<?php echo $row['name']; ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="email" name="email" value="<?php echo $row['email']; ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Mobile</label>
                        <input type="text" name="mobile" value="<?php echo $row['mobile']; ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Address</label>
                        <textarea name="address"  id="" cols="" rows="3" class="form-control"><?php echo $row['address']; ?></textarea>
                    </div><br>
                    <div class="form-group">
                        <button type="submit" name="update" class="btn btn-primary">Submit</button>
                        <a href="records.php" class="btn btn-danger">Return</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoWe/6oMVemdAVTMs2xqW4mwXrXsW0L84Iytr2wi5v2QjrP/xp" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js" integrity="sha384-cn7l7gDp0eyniUwwAZgrzD06kc/tftFf19TOAs2zVinnD/C7E91j9yyk5//jjpt/" crossorigin="anonymous"></script>
    -->
  </body>
</html>