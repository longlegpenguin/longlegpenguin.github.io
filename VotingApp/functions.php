<?php

/* From practice */

/**
 * Redirects you to a page and stops the originating script.
 * @param string $page The page you want to redirect the user to.
 */
function redirect($page){
    header('Location: ' . $page);
    die;
}

/**
 * Read a JSON file and converts the content to and Array or an Object.
 * @param string $filename The name of the JSON file with the extension.
 * @param bool|null $associative [optional] When TRUE, returned objects will be converted into associative arrays.
 * @return (Array|Object) Depending on the content of the JSON file, an Array or an Object of the data that was inside.
 */
function json_read($filename, $associative = false){
    return json_decode(file_get_contents($filename), $associative);
}

/**
 * Write an Array or Object into a JSON file. It OVERWRITES the content of the file.
 * @param string $filename The name of the JSON file with the extension.
 * @param (Array|Object) $data The data to be converted to string.
 */
function json_write($filename, $data){
    file_put_contents($filename,json_encode($data, JSON_PRETTY_PRINT));
}

/**
 * Tells whether the user a user is already logged in or not using a session.
 * @return bool Is anyone logged in?
 */
function auth_is_logged_in(){
    $user = $_SESSION['user'] ?? '';
    return trim($user) != '';
}


/* MY OWN */

// ---- User related --------
/**
 * Checks if a given user has voted for a given poll.
 * @param int $uid 
 * @param int $pid
 */
function has_voted($uid, $pid) {
    $all_polls = json_read('polls.json', true);
    return in_array($uid, $all_polls[$pid]['voted']);
}

/**
 * Get the answer of a user of a specific poll.
 * @param int $uid user id of the user.
 * @param int $pid poll id of the poll.
 * @return string answer of the user for the poll.
 */
function get_vote_ans($uid, $pid) {
    $all_users = json_read('users.json', true);
    return $all_users[$uid]['votes'][$pid];
}

/**
 * Gets username by user id.
 * @param int $uid user id
 * @return string username
 */
function get_username_by($uid) {
    $all_users = json_read('users.json', true);
    // var_dump($all_users[$uid]['username'] ?? 'User');
    return $all_users[$uid]['username'] ?? 'User';
}

/**
 * Get the user asso array by username.
 * @param string $uname username.
 * @return array user if user exists.
 * @return null if user not exists.
 */
function get_user_by($uname) {
    $users = json_read('users.json', true);
    foreach ($users as $user) {
        if ($user['username'] == $uname) return $user;
    }
    return null;
}

/**
 * Checks if a given user is admin.
 * @param int $uid user id.
 * @return bool true if is admin, otherwise false.
 */
function is_admin($uid) {
    $all_users = json_read('users.json', true);
    return $all_users[$uid]['isAdmin'] ?? false;
}

/**
 * Register a new user.
 * @param array $udata data of the new user (from form)
 */
function register($udata) {
    $all_users = json_read('users.json', true);
    $new_uid = max(array_keys($all_users)) + 1;
    $new_user = [
        "id" => $new_uid,
        "username" => $udata['uname'],
        "email" => $udata['email'],
        "password" => $udata['pwsd1'],
        "isAdmin" => false,
        "votes" => []
    ];
    $all_users[$new_uid] = $new_user;
    json_write('users.json', $all_users);
}

// ---- Poll related --------

/**
 * Filter the valid and expired polls according to the current time.
 * @param array $polls array contains all polls.
 * @param array $good_polls Pass in empty, filled with valid.
 * @param array $expired_polls Pass in empty, filled with expired.
 */
function filter_polls($polls, &$good_polls, &$expired_polls) {
    // $cur_date = date("Y-m-d", time());
    // $good_polls = array_filter($polls, fn ($poll) => date("Y-m-d", strtotime($poll['deadline'])) >= $cur_date);
    // $expired_polls = array_filter($polls, fn ($poll) => date("Y-m-d", strtotime($poll['deadline'])) < $cur_date);
    $good_polls = array_filter($polls, function($poll) {
        $cur_date = date("Y-m-d", time());
        return date("Y-m-d", strtotime($poll['deadline'])) >= $cur_date;
    });
    $expired_polls = array_filter($polls, function($poll) {
        $cur_date = date("Y-m-d", time());
        return date("Y-m-d", strtotime($poll['deadline'])) < $cur_date;
    });
    // var_dump($expired_polls);
    // var_dump($polls);
}

/**
 * Gets the poll by poll id.
 * @param int $pid
 * @return array poll if poll exists.
 * @return false if poll not exists.
 */
function get_poll_by_id($pid) {
    $all_polls = json_read('polls.json', true);
    foreach ($all_polls as $poll) {
        if ($poll['id'] == $pid) {
            return $poll;
        }
    }
    return false;
}

/**
 * Adds a new poll with given data.
 * @param array poll data.
 */
function add_poll($poll_data) {
    $all_polls = json_read('polls.json', true);
    $all_options = explode("\n", str_replace("\r", "", $poll_data['options']));
    $all_ans = [];
    foreach($all_options as $opt) {
        $all_ans[$opt] = 0;
    }
    $new_id = max(array_keys($all_polls)) + 1;
    $new_poll = [
        "id" => $new_id,
        "question" => $poll_data['question'],
        "options" => $all_options,
        "isMultiple" => $poll_data['multi_single'] == 'single' ? false : true,
        "createdAt" => date("Y-m-d", time()),
        "deadline" => date("Y-m-d", $poll_data['deadline']),
        "answers" => $all_ans,
        "voted" => []
    ];
    $all_polls[$new_id] = $new_poll;
    json_write('polls.json', $all_polls);
}

/**
 * Deletes the poll of the given id.
 * Removes poll details from polls.json 
 * as well as the related vote history in users.json
 * @param int $pid poll id to be deleted.
 */
function delete($pid) {
    $all_polls = json_read('polls.json', true);
    $all_users = json_read('users.json', true);
    unset($all_polls[$pid]);
    foreach($all_users as $uid => $user) {
        if (has_voted($uid, $pid)) {
            unset($all_users[$uid]['votes'][$pid]);
        }
    }
    json_write('polls.json', $all_polls);
    json_write('users.json', $all_users);
}

// ---- Helper functions -------
/**
 * Callback function used in array sort.
 * Compares the creation date of two polls.
 * @param array $elem1 poll1
 * @param array $elem2 poll2
 */
function date_compare($elem1, $elem2) {
    $d1 = strtotime($elem1['createdAt']);
    $d2 = strtotime($elem2['createdAt']);
    return $d2 - $d1;
}

/**
 * Prepares the url regarding the login details.
 * @param int $pid id of the poll to be redirect.
 * @return string if logged in, to voting page. Otherwise, to login page.
 */
function get_url($pid) {
    /* voting_page.php?pollid=<?= $poll['id'] ?> */
    if (auth_is_logged_in()) {
        return "voting_page.php?pollid=" . $pid;
    } else {
        return "login_page.php";
    }
}