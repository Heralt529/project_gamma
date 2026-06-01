<?php
// auth_basic.php

function auth($request, $resource) {
    // Проверяем, есть ли уже авторизация в сессии
    if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        $request['user']['login'] = $_SESSION['admin_login'];
        $request['user']['role'] = 'admin';
        return false; // авторизован, продолжаем
    }

    // HTTP Basic Auth
    if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="Admin Panel"');
        return array(
            'headers' => array('HTTP/1.1 401 Unauthorized'),
            'entity' => theme('401')
        );
    }

    $login = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];

    global $db;
    $stmt = $db->prepare("SELECT id, login, pass_hash FROM admin_users WHERE login = ?");
    $stmt->execute([$login]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && md5($password) === $admin['pass_hash']) {
        session_start();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login'] = $admin['login'];
        $_SESSION['admin_id'] = $admin['id'];
        return false; // авторизация успешна
    }

    // Неверные данные
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    return array(
        'headers' => array('HTTP/1.1 401 Unauthorized'),
        'entity' => theme('401')
    );
}
