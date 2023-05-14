<?php
session_start();
require_once 'functions.php';

$error_dict = json_read('errors.json', true);
$all_polls = json_read('polls.json', true);
$all_users = json_read('users.json', true);
$cur_uid = $_SESSION['user'];
$cur_user = $all_users[$cur_uid];

$errors = [];
$poll_id = $_GET['pollid'] ?? -1;

if (!($cur_poll = get_poll_by_id($poll_id))) {
    $errors[] = $error_dict['POLL_NOT_FOUND'];
} else if (strtotime($cur_poll['deadline']) < strtotime(date('Y-m-d', time()))) {
    $errors[] = $error_dict['POLL_EXPIRED'];
}
if (isset($_POST['submit_btn'])) {
    if (isset($_POST['poll_ans'])) {
        $ans = $_POST['poll_ans'];

        // check if already voted.
        if (has_voted($cur_uid, $poll_id)) {
            $prev_ans = get_vote_ans($cur_uid, $poll_id);
            $cur_poll['answers'][$prev_ans]--;
        } else {
            $cur_poll['voted'][] = $cur_uid;
        }

        $cur_poll['answers'][$ans]++;
        $cur_user['votes'][$poll_id] = $ans;

        $all_polls[$cur_poll['id']] = $cur_poll;
        json_write('polls.json', $all_polls);
        $all_users[$cur_uid] = $cur_user;
        // var_dump($all_users);
        json_write('users.json', $all_users);

        $message = "Successfully voted \"".$ans."\", you can change your answer by submitting again.";
        $success = true;
    } else {
        $message = "You must select one of the options!";
        $success = false;
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
        <?php if (isset($success)) : ?>
            <div class="p-3 mb-2 <?= $success ? 'bg-success' : 'bg-danger' ?>  text-white">
                <?= $message ?? '' ?>
            </div>
        <?php endif ?>
        <?php if ($errors == []) : ?>
            <form action="" method="POST" novalidate>
                <p><?= $cur_poll['question'] ?></p>

                <?php foreach ($cur_poll['options'] as $opt) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="poll_ans" value="<?= $opt ?>" <?= ($ans??'') == $opt ? 'checked':'' ?>>
                        <label class="form-check-label" for="poll_ans">
                            <?= $opt ?>
                        </label>
                    </div>
                <?php endforeach ?>
                <br>
                <button type="submit" class="btn btn-primary" name="submit_btn">submit</button>
            </form>
        <?php else : ?>
            <div>
                <?php foreach ($errors as $err) : ?>
                    <h1><?= $err ?></h1>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</body>

</html>