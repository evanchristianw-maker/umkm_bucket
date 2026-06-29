<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id_admin">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#3B4A1F;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .card{
            width:380px;
            border:none;
            border-radius:15px;
        }

        .btn-login{
            background:#3B4A1F;
            color:white;
        }

        .btn-login:hover{
            background:#2d3917;
            color:white;
        }
    </style>
</head>

<body>

<div class="card shadow p-4">

    <h3 class="text-center mb-4">
        Login Admin
    </h3>

    <?php
    if(isset($_SESSION['error'])){
        ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; ?>
        </div>
        <?php
        unset($_SESSION['error']);
    }
    ?>

    <form action="proses/login.php" method="POST">

        <div class="mb-3">
            <label>Username</label>

            <input
                type="text"
                name="username"
                class="form-control"
                required>
        </div>

        <div class="mb-3">
            <label>Password</label>

            <input
                type="password"
                name="password"
                class="form-control"
                required>
        </div>

        <button class="btn btn-login w-100">
            Login
        </button>

    </form>

</div>

</body>
</html>

    

