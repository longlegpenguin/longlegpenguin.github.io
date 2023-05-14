<?php
require_once 'functions.php';
session_start();

$error_dict = json_read('errors.json', true);

if (count($_POST) > 0) {

    $form_data = [
        "question" => $_POST['question'] ?? '',
        "options" => $_POST['options'] ?? [],
        "deadline" => strtotime($_POST['deadline']) ?? time(),
        "multi_single" => $_POST['multi_single'] ?? ''
    ];
    
    if (is_admin($_SESSION['user'] ?? '')) {
        add_poll($form_data);
        redirect('index.php?new=1');
    } else {
        $error = $error_dict['401'];
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
    <div style="width:70%" class="position-absolute top-50 start-50 translate-middle mt-1 shadow-lg p-3 mb-5 bg-body rounded">
        <div><a href="index.php">Back to main</a></div>
        <br>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif ?>
        <h1>Poll Creation</h1>
        <form action="" method="POST" novalidate>
            <div class="mb-3">
                <label for="question" class="form-label">Question</label>
                <input type="text" name="question" class="form-control" id="question">
            </div>
            <div class="mb-3">
                <label for="options" class="form-label" aria-describedby="optionHelp">Options</label>
                <textarea class="form-control" id="options" name="options" rows="5"></textarea>
                <div id="optionHelp" class="form-text">Please enter the options seperate by newlines.</div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="multi_single" value="single" <?= true ? 'checked' : '' ?>>
                    <label class="form-check-label" for="multi_single">
                        Single Choice Only
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="multi_single" value="multiple" <?= false ? 'checked' : '' ?>>
                    <label class="form-check-label" for="multi_single">
                        Allow Multiple Choices
                    </label>
                </div>
            </div>
            <div class="mb-3">
                <label for="deadline" class="form-label">Deadline</label>
                <input type="date" name="deadline" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

</html>