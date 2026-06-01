<?php
// admin.php

function admin_get($request) {
    global $db;
    
    // Проверяем, есть ли параметр edit
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        return admin_edit_form($request, $_GET['edit']);
    }
    
    // Получаем всех пользователей с их языками
    $users_data = [];
    
    $stmt = $db->query("SELECT id, name, phone, email, birthdate, sex, biography, login, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $lang_stmt = $db->prepare("SELECT language FROM user_languages WHERE user_id = ?");
    
    foreach ($users as $user) {
        $lang_stmt->execute([$user['id']]);
        $languages = $lang_stmt->fetchAll(PDO::FETCH_COLUMN);
        $user['languages'] = implode(', ', $languages);
        $users_data[] = $user;
    }
    
    // Статистика по языкам
    $stat_stmt = $db->query("
        SELECT language, COUNT(*) as count 
        FROM user_languages 
        GROUP BY language 
        ORDER BY count DESC
    ");
    $language_stats = $stat_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $total_users = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Получаем сообщения из кук
    $message = '';
    $message_type = '';
    if (!empty($_COOKIE['admin_message'])) {
        $message = $_COOKIE['admin_message'];
        $message_type = $_COOKIE['admin_message_type'] ?? 'info';
        setcookie('admin_message', '', time() - 3600);
        setcookie('admin_message_type', '', time() - 3600);
    }
    
    return theme('admin_panel', [
        'users' => $users_data,
        'language_stats' => $language_stats,
        'total_users' => $total_users,
        'message' => $message,
        'message_type' => $message_type
    ]);
}

// Форма редактирования
function admin_edit_form($request, $id) {
    global $db;
    
    $id = intval($id);
    
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        setcookie('admin_message', 'Пользователь не найден', time() + 5);
        setcookie('admin_message_type', 'error', time() + 5);
        return redirect('admin');
    }
    
    $lang_stmt = $db->prepare("SELECT language FROM user_languages WHERE user_id = ?");
    $lang_stmt->execute([$id]);
    $user_languages = $lang_stmt->fetchAll(PDO::FETCH_COLUMN);
    $user['languages'] = $user_languages;
    
    $all_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];
    
    return theme('admin_edit', [
        'user' => $user,
        'all_languages' => $all_languages
    ]);
}

// Обработчик POST (и для удаления, и для обновления через _method)
function admin_post($request, $id = null) {
    global $db;
    
    // Если передан _method=PUT — вызываем обновление
    if (isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
        return admin_update($request, $id);
    }
    
    // Иначе — удаление
    if ($id === null) {
        setcookie('admin_message', 'ID пользователя не указан', time() + 5);
        setcookie('admin_message_type', 'error', time() + 5);
        return redirect('admin');
    }
    
    $id = intval($id);
    
    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            setcookie('admin_message', 'Пользователь удалён', time() + 5);
            setcookie('admin_message_type', 'success', time() + 5);
        } else {
            setcookie('admin_message', 'Пользователь не найден', time() + 5);
            setcookie('admin_message_type', 'error', time() + 5);
        }
    } catch (PDOException $e) {
        setcookie('admin_message', 'Ошибка удаления: ' . $e->getMessage(), time() + 5);
        setcookie('admin_message_type', 'error', time() + 5);
    }
    
    return redirect('admin');
}

// Функция обновления пользователя
function admin_update($request, $id) {
    global $db;
    
    $id = intval($id);
    if (!$id) {
        setcookie('admin_message', 'ID пользователя не указан', time() + 5);
        setcookie('admin_message_type', 'error', time() + 5);
        return redirect('admin');
    }
    
    $data = $_POST;
    
    // Валидация
    $errors = validate_admin_form($data);
    if (!empty($errors)) {
        $_SESSION['admin_edit_errors'] = $errors;
        $_SESSION['admin_edit_data'] = $data;
        return redirect('admin?edit=' . $id);
    }
    
    try {
        $db->beginTransaction();
        
        $stmt = $db->prepare("
            UPDATE users 
            SET name = ?, phone = ?, email = ?, birthdate = ?, sex = ?, biography = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $data['name'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['birthdate'] ?? null,
            $data['sex'],
            $data['biography'] ?? null,
            $id
        ]);
        
        // Обновляем языки
        $del_stmt = $db->prepare("DELETE FROM user_languages WHERE user_id = ?");
        $del_stmt->execute([$id]);
        
        if (!empty($data['languages']) && is_array($data['languages'])) {
            $lang_stmt = $db->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
            foreach ($data['languages'] as $lang) {
                $lang_stmt->execute([$id, $lang]);
            }
        }
        
        $db->commit();
        
        setcookie('admin_message', 'Пользователь успешно обновлён', time() + 5);
        setcookie('admin_message_type', 'success', time() + 5);
        
    } catch (PDOException $e) {
        $db->rollBack();
        setcookie('admin_message', 'Ошибка обновления: ' . $e->getMessage(), time() + 5);
        setcookie('admin_message_type', 'error', time() + 5);
    }
    
    return redirect('admin');
}

// Валидация
function validate_admin_form($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors['name'] = 'Имя обязательно';
    } elseif (strlen($data['name']) > 150) {
        $errors['name'] = 'Имя не должно превышать 150 символов';
    }
    
    if (!empty($data['phone']) && !preg_match('/^[\+0-9\s\-\(\)]{10,20}$/', $data['phone'])) {
        $errors['phone'] = 'Некорректный телефон';
    }
    
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email';
    }
    
    if (!empty($data['birthdate'])) {
        $date = DateTime::createFromFormat('Y-m-d', $data['birthdate']);
        if (!$date || $date->format('Y-m-d') !== $data['birthdate']) {
            $errors['birthdate'] = 'Некорректная дата';
        }
    }
    
    if (empty($data['sex']) || !in_array($data['sex'], ['male', 'female'])) {
        $errors['sex'] = 'Выберите пол';
    }
    
    return $errors;
}
