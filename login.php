<<<<<<< HEAD
<!DOCTYPE html>
<html>
<head>
    <title>Login UMKM Bucket</title>
</head>
<body>

<h2>Login Admin</h2>

<form action="proses/login.php" method="POST">

    Username <br>
    <input type="text" name="username" required>
    <br><br>

    Password <br>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">
        Login
    </button>

</form>

</body>
=======
<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

<div class="login-container">

    <div class="login-card">

        <h1>UMKM Bucket</h1>

        <p>Login Admin</p>

        <form action="proses/login.php" method="POST">

            <label>Username</label>

            <input type="text"
                   name="username"
                   required>

            <label>Password</label>

            <input type="password"
                   name="password"
                   required>

            <button type="submit"
                    class="btn btn-primary">
                Login
            </button>

        </form>

    </div>

</div>

</body>
>>>>>>> main
</html>