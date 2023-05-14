<?php
require_once 'functions.php';
session_start();

if (auth_is_logged_in()) redirect('index.php');

// Process if form is submitted.
if (count($_POST) > 0) {
    $form_data = [
        'uname' => trim($_POST['uname'] ?? ''),
        'pwsd' => trim($_POST['pwsd'] ?? ''),
    ];

    $cur_user = get_user_by($form_data['uname']);

    if (!isset($cur_user)) {
        $uname_error = "User name does not exist!";
    } else if ($cur_user['password'] != $form_data['pwsd']) {
        $pwsd_error = "Incorrect password!";
    } else {
        // logs the user in
        $_SESSION['user'] = $cur_user['id'];
        redirect('index.php');
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
        <h1>Log In</h1>
        <form action="" method="POST" novalidate>
            <div class="mb-3">
                <label for="uname" class="form-label">User name</label>
                <input type="text" class="form-control" name="uname" id="uname" value="<?= $form_data['uname']??''?>">
                <?php if (isset($uname_error)) : ?>
                    <div class="alert alert-danger"><?= $uname_error ?></div>
                <?php endif ?>
            </div>
            <div class="mb-3">
                <label for="pwsd" class="form-label">Password</label>
                <input type="password" class="form-control" name="pwsd" id="pwsd">
                <?php if (isset($pwsd_error)) : ?>
                    <div class="alert alert-danger"><?= $pwsd_error ?></div>
                <?php endif ?>
            </div>
            <button type="submit" class="btn btn-primary">Logon</button>
            <span class="px-3"><a href="register_page.php">Not yet resgistered? Go to register!</a></span>
        </form>
    </div>
</body>

</html>