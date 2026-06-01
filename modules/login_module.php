<?php
// login_module.php

function login_module_get($request) {
    // Если уже авторизован, редирект на форму
    if (!empty($request['user']['login'])) {
        $c = array(
            'error' => false,
            'message' => 'Вы уже авторизованы. <a href="?q=logout">Выйти?</a>'
        );
        return theme('login', $c);
    }
    
    $error = !empty($_COOKIE['login_error']);
    if ($error) {
        setcookie('login_error', '', 100000);
    }
    
    $c = array('error' => $error);
    return theme('login', $c);
}

function login_module_post($request) {
    global $db;
    
    $login = $_POST['login'] ?? '';
    $pass = $_POST['pass'] ?? '';
    
    if (empty($login) || empty($pass)) {
        setcookie('login_error', '1', time() + 3600);
        return redirect('login');
    }
    
    try {
        $stmt = $db->prepare("SELECT id, login, pass_hash FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && md5($pass) === $user['pass_hash']) {
            session_start();
            $_SESSION['login'] = $user['login'];
            $_SESSION['uid'] = $user['id'];
            return redirect('form');
        } else {
            setcookie('login_error', '1', time() + 3600);
            return redirect('login');
        }
    } catch (PDOException $e) {
        setcookie('login_error', '1', time() + 3600);
        return redirect('login');
    }
}
?>
