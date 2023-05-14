<?php
session_start();
require_once 'functions.php';

if (count($_POST) > 0) {
    $form_data = [
        'uname' => trim($_POST['uname'] ?? ''),
        'pwsd1' => trim($_POST['pwsd1'] ?? ''),
        'pwsd2' => trim($_POST['pwsd2'] ?? ''),
        'email' => trim($_POST['email'] ?? '')
    ];

    $good = true;
    // check uname
    if ($form_data['uname'] == '') {
        $uname_error = "Username cannot be empty!";
        $good = false;
    }
    // check email address
    if ($form_data['email'] == '') {
        $email_error = "Email cannot be empty!";
        $good = false;
    } else if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format!";
        $good = false;
    }
    // check password match
    if ($form_data['pwsd1'] == '') {
        $pwsd_error = "Password cannot be empty!";
        $good = false;
    } else if ($form_data['pwsd1'] !== $form_data['pwsd2']) {
        $pwsd_error = "Passwords mismatched!";
        $good = false;
    }
    if ($good) {
        register($form_data);
        redirect('login_page.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
    </style>
</head>

<body class="container-md">
    <div style="width:50%" class="position-absolute top-50 start-50 translate-middle mt-1 shadow-lg p-3 mb-5 bg-body rounded">
        <div><a href="index.php">Back to main</a></div>
        <br>
        <h1>Register</h1>
        <form action="" method="POST" novalidate>
            <div class="mb-3">
                <label for="uname" class="form-label">User name</label>
                <input type="text" class="form-control" name="uname" id="uname" value="<?= $form_data['uname'] ?? '' ?>">
                <?php if (isset($uname_error)) : ?>
                    <div class="alert alert-danger"><?= $uname_error ?></div>
                <?php endif ?>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $form_data['email'] ?? '' ?>" aria-describedby="emailHelp">
                <?php if (isset($email_error)) : ?>
                    <div class="alert alert-danger"><?= $email_error ?></div>
                <?php endif ?>
            </div>
            <div class="mb-3">
                <label for="pwsd1" class="form-label">Password</label>
                <input type="password" class="form-control" id="pwsd1" name="pwsd1" value="<?= $form_data['pwsd1'] ?? '' ?>">
                <?php if (isset($pwsd_error)) : ?>
                    <div class="alert alert-danger"><?= $pwsd_error ?></div>
                <?php endif ?>
            </div>
            <div class="mb-3">
                <label for="pwsd2" class="form-label">Password Again</label>
                <input type="password" class="form-control" id="pwsd2" name="pwsd2" value="<?= $form_data['pwsd2'] ?? '' ?>">
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <span class="px-3"><a href="login_page.php">Already resgistered? Go to login!</a></span>
        </form>
    </div>
</body>

</html>