<?php
// logout.php

function logout_post($request) {
    session_start();
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    session_destroy();
    return redirect('form');
}

function logout_get($request) {
    return redirect('form');
}
?>
