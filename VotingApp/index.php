<?php
require_once 'functions.php';
session_start();
$cur_uid = $_SESSION['user'] ?? '';

// Preparing the polls to be listed.
$all_polls = json_read('polls.json', true);
$good_polls = [];
$expired_polls = [];
filter_polls($all_polls, $good_polls, $expired_polls);
usort($good_polls, 'date_compare');
usort($expired_polls, 'date_compare');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Web App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body class="container-md">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Voting</a>
            <div class="d-flex">
                <span class="d-flex navbar-text px-3">Hi, <?= get_username_by($cur_uid) ?>!</span>
                <?php if ($cur_uid == '') : ?>
                    <form action="login_page.php" class="d-flex">
                        <button class="btn btn-outline-success" type="submit">Logon</button>
                    </form>
                <?php else : ?>
                    <form action="logout_query.php" class="d-flex">
                        <button class="btn btn-outline-danger" type="submit">Logout</button>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </nav>

    <div style="width:100%" class="mt-4 p-3 mb-3 bg-body rounded">
        <h1>Welcome to Voting App</h1>
        <p>This a web application where logged-in users can cast their votes on polls (questionnaires/forms).</p>
        <p>Register/Login and vote for your favourite polls! </p>
    </div>
    
    <!-- Poll creations section -->
    <?php if (is_admin($cur_uid)) : ?>
        <div style="width:100%" class="mt-1 shadow-sm p-3 mb-5 bg-body rounded">
            <form action="voting_creation_page.php">
                <button type="submit" class="btn btn-primary" name="cr_btn">Create a New Poll</button>
                <?php if (isset($_GET['new'])) : ?>
                    <span class="text-muted">New Poll Created!</span>
                <?php endif ?>
            </form>
        </div>
    <?php endif ?>

    <!-- Active Polls Section -->
    <h2>Active Polls</h2>
    <div class="list-group">
        <?php foreach ($good_polls as $poll) : ?>
            <a href="<?= get_url($poll['id']) ?>" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><?= $poll['question'] ?></h5>
                    <small class="text-muted">created: <?= $poll['createdAt'] ?></small>
                    <small class="text-muted">deadline: <?= $poll['deadline'] ?></small>
                </div>
                <p class="mb-1"><?= has_voted($cur_uid, $poll['id']) ? 'Already voted! Click to modify!' : 'Not yet voted! Click to vote!' ?></p>
                <small class="text-muted">Poll ID: <?= $poll['id'] ?></small>
            </a>
            <?php if (is_admin($cur_uid)) : ?>
                <div class="mb-3 px-3"><a href="delete.php?pid=<?= $poll['id'] ?>">Delete this Poll</a></div>
            <?php endif ?>

        <?php endforeach ?>
    </div>
    <hr>

    <!-- Closed Polls Section -->
    <h2>Closed Polls</h2>
    <div class="list-group">
        <?php foreach ($expired_polls as $poll) : ?>
            <a href="#" class="list-group-item list-group-item-action disabled">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><?= $poll['question'] ?></h5>
                    <small class="text-muted">created: <?= $poll['createdAt'] ?></small>
                    <small class="text-muted">deadline: <?= $poll['deadline'] ?></small>
                </div>
                <p class="mb-1">Poll has expired. Check the results below.</p>
                <ul>
                    <?php foreach ($poll['answers'] as $ans => $cnt) : ?>
                        <li><?= $ans ?>: <?= $cnt ?></li>
                    <?php endforeach ?>
                </ul>
                <small class="text-muted">Poll ID: <?= $poll['id'] ?></small>
            </a>
            <?php if (is_admin($cur_uid)) : ?>
                <div class="mb-3 px-3"><a href="delete.php?pid=<?= $poll['id'] ?>">Delete this Poll</a></div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
    <hr>
</body>

</html>