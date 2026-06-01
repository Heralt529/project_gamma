<?php
// admin.php

function admin_get($request) {
    global $db;
    
    // Получаем всех пользователей с их языками
    $users_data = [];
    
    // 1. Получаем пользователей
    $stmt = $db->query("SELECT id, name, phone, email, birthdate, sex, biography, login, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Получаем языки для каждого пользователя
    $lang_stmt = $db->prepare("SELECT language FROM user_languages WHERE user_id = ?");
    
    foreach ($users as $user) {
        $lang_stmt->execute([$user['id']]);
        $languages = $lang_stmt->fetchAll(PDO::FETCH_COLUMN);
        $user['languages'] = implode(', ', $languages);
        $users_data[] = $user;
    }
    
    // 3. Статистика по языкам
    $stat_stmt = $db->query("
        SELECT language, COUNT(*) as count 
        FROM user_languages 
        GROUP BY language 
        ORDER BY count DESC
    ");
    $language_stats = $stat_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Общая статистика
    $total_stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $total_users = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    return theme('admin_panel', [
        'users' => $users_data,
        'language_stats' => $language_stats,
        'total_users' => $total_users,
        'message' => !empty($_COOKIE['admin_message']) ? $_COOKIE['admin_message'] : '',
        'message_type' => !empty($_COOKIE['admin_message_type']) ? $_COOKIE['admin_message_type'] : ''
    ]);
}

// Удаление пользователя (DELETE через POST с _method)
function admin_post($request, $id = null) {
    global $db;
    
    if ($id === null) {
        setcookie('admin_message', 'ID пользователя не указан', time() + 5);
        setcookie('admin_message_type', 'error', time() + 5);
        return redirect('admin');
    }
    
    $id = intval($id);
    
    // Проверяем, есть ли _method DELETE
    $method = isset($_POST['_method']) ? strtoupper($_POST['_method']) : 'POST';
    
    if ($method === 'DELETE') {
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
    
    // Если это редактирование — перенаправляем на GET с параметром edit
    if (isset($_POST['edit_user'])) {
        return redirect('admin/' . $id . '/edit');
    }
    
    return redirect('admin');
}

// Редактирование пользователя
function admin_edit_get($request, $id) {
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
    
    // Получаем языки пользователя
    $lang_stmt = $db->prepare("SELECT language FROM user_languages WHERE user_id = ?");
    $lang_stmt->execute([$id]);
    $user_languages = $lang_stmt->fetchAll(PDO::FETCH_COLUMN);
    $user['languages'] = $user_languages;
    
    // Список всех языков для формы
    $all_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];
    
    return theme('admin_edit', [
        'user' => $user,
        'all_languages' => $all_languages
    ]);
}

// Обновление пользователя (PUT)
function admin_put($request, $id) {
    global $db;
    
    $id = intval($id);
    
    // Получаем данные из php://input для PUT
    parse_str(file_get_contents('php://input'), $put_data);
    
    // Или из POST если пришло как multipart
    if (empty($put_data)) {
        $put_data = $_POST;
    }
    
    // Валидация
    $errors = validate_admin_form($put_data);
    if (!empty($errors)) {
        $_SESSION['admin_edit_errors'] = $errors;
        $_SESSION['admin_edit_data'] = $put_data;
        return redirect('admin/' . $id . '/edit');
    }
    
    try {
        $db->beginTransaction();
        
        // Обновляем пользователя
        $stmt = $db->prepare("
            UPDATE users 
            SET name = ?, phone = ?, email = ?, birthdate = ?, sex = ?, biography = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $put_data['name'],
            $put_data['phone'] ?? null,
            $put_data['email'] ?? null,
            $put_data['birthdate'] ?? null,
            $put_data['sex'],
            $put_data['biography'] ?? null,
            $id
        ]);
        
        // Обновляем языки
        $del_stmt = $db->prepare("DELETE FROM user_languages WHERE user_id = ?");
        $del_stmt->execute([$id]);
        
        if (!empty($put_data['languages']) && is_array($put_data['languages'])) {
            $lang_stmt = $db->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
            foreach ($put_data['languages'] as $lang) {
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

// Вспомогательная функция валидации для админки
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
