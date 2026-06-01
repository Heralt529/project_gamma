<?php
// api.php - модуль для работы с REST API

function api_options($request) {
    // Для CORS preflight
    return array(
        'headers' => array(
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
        )
    );
}

// GET - получение данных пользователя (для авторизованных)
function api_get($request, $user_id = null) {
    global $db;
    
    // Проверка авторизации
    if (empty($request['user']['login'])) {
        return array(
            'headers' => array('HTTP/1.1 401 Unauthorized', 'Content-Type' => 'application/json'),
            'entity' => json_encode(array('error' => 'Требуется авторизация'))
        );
    }
    
    try {
        $stmt = $db->prepare("SELECT id, name, phone, email, birthdate, sex, biography FROM users WHERE login = ?");
        $stmt->execute([$request['user']['login']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userData) {
            return array(
                'headers' => array('HTTP/1.1 404 Not Found', 'Content-Type' => 'application/json'),
                'entity' => json_encode(array('error' => 'Пользователь не найден'))
            );
        }
        
        // Получаем языки
        $lang_stmt = $db->prepare("SELECT language FROM user_languages WHERE user_id = ?");
        $lang_stmt->execute([$userData['id']]);
        $languages = $lang_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $userData['languages'] = $languages;
        unset($userData['id']);
        
        return array(
            'headers' => array('Content-Type' => 'application/json'),
            'entity' => json_encode($userData)
        );
        
    } catch (PDOException $e) {
        return array(
            'headers' => array('HTTP/1.1 500 Internal Server Error', 'Content-Type' => 'application/json'),
            'entity' => json_encode(array('error' => 'Ошибка сервера: ' . $e->getMessage()))
        );
    }
}

// POST - создание новой записи
function api_post($request) {
    global $db;
    
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $request['post'];
    }
    
    // Валидация
    $errors = validate_form_data($data);
    if (!empty($errors)) {
        return array(
            'headers' => array('HTTP/1.1 400 Bad Request', 'Content-Type' => 'application/json'),
            'entity' => json_encode(array('errors' => $errors))
        );
    }
    
    try {
        $db->beginTransaction();
        
        // Генерация логина и пароля
        $login = 'user_' . substr(md5(uniqid(rand(), true)), 0, 8);
        $password = substr(md5(uniqid(rand(), true)), 0, 8);
        $pass_hash = md5($password);
        
        $stmt = $db->prepare("INSERT INTO users (name, phone, email, birthdate, sex, biography, login, pass_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['birthdate'] ?? null,
            $data['sex'],
            $data['biography'] ?? null,
            $login,
            $pass_hash
        ]);
        
        $user_id = $db->lastInsertId();
        
        // Сохраняем языки
        if (!empty($data['languages'])) {
            $lang_stmt = $db->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
            foreach ($data['languages'] as $lang) {
                $lang_stmt->execute([$user_id, $lang]);
            }
        }
        
        $db->commit();
        
        return array(
            'headers' => array('HTTP/1.1 201 Created', 'Content-Type' => 'application/json'),
            'entity' => json_encode(array(
                'success' => true,
                'message' => 'Данные успешно сохранены',
                'login' => $login,
                'password' => $password,
                'profile_url' => url('profile')
            ))
        );
        
    } catch (PDOException $e) {
        $db->rollBack();
        return array(
            'headers' => array('HTTP/1.1 500 Internal Server Error', 'Content-Type' => 'application/json'),
            'entity' => json_encode(array('error' => 'Ошибка сервера: ' . $e->getMessage()))
        );
    }
}

// PUT - обновление данных (требует авторизации)
function api_put($request) {
    global $db;
    
    // Проверка авторизации
    if (empty($request['user']['login'])) {
        return array(
            'headers' => array('HTTP/1.1 401 Unauthorized', 'Content-Type' => 'application/json'),
            'entity' => json_encode(array('error' => 'Требуется авторизация'))
        );
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $request['post'];
    }
    
    // Валидация (без пароля)
    $errors = validate_form_data($data, true);
    if (!empty($errors)) {
        return array(
            'headers' => array('HTTP/1.1 400 Bad Request', 'Content-Type' => 'application/json'),
            'entity' => json_encode(array('errors' => $errors))
        );
    }
    
    try {
        $db->beginTransaction();
        
        // Получаем ID пользователя
        $stmt = $db->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$request['user']['login']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return array(
                'headers' => array('HTTP/1.1 404 Not Found', 'Content-Type' => 'application/json'),
                'entity' => json_encode(array('error' => 'Пользователь не найден'))
            );
        }
        
        // Обновляем данные
        $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, email = ?, birthdate = ?, sex = ?, biography = ? WHERE id = ?");
        $stmt->execute([
            $data['name'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['birthdate'] ?? null,
            $data['sex'],
            $data['biography'] ?? null,
            $user['id']
        ]);
        
        // Обновляем языки
        $del_stmt = $db->prepare("DELETE FROM user_languages WHERE user_id = ?");
        $del_stmt->execute([$user['id']]);
        
        if (!empty($data['languages'])) {
            $lang_stmt = $db->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
            foreach ($data['languages'] as $lang) {
                $lang_stmt->execute([$user['id'], $lang]);
            }
        }
        
        $db->commit();
        
        return array(
            'headers' => array('Content-Type' => 'application/json'),
            'entity' => json_encode(array(
                'success' => true,
                'message' => 'Данные успешно обновлены'
            ))
        );
        
    } catch (PDOException $e) {
        $db->rollBack();
        return array(
            'headers' => array('HTTP/1.1 500 Internal Server Error', 'Content-Type' => 'application/json'),
            'entity' => json_encode(array('error' => 'Ошибка сервера: ' . $e->getMessage()))
        );
    }
}

// Функция валидации данных
function validate_form_data($data, $is_update = false) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors['name'] = 'Введите корректное имя.';
    } elseif (strlen($data['name']) > 150) {
        $errors['name'] = 'Имя не должно превышать 150 символов.';
    } elseif (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]+$/u', $data['name'])) {
        $errors['name'] = 'Имя должно содержать только буквы.';
    }
    
    if (!empty($data['phone']) && !preg_match('/^[\+0-9\s\-\(\)]{10,20}$/', $data['phone'])) {
        $errors['phone'] = 'Введите корректный номер телефона.';
    }
    
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email.';
    }
    
    if (!empty($data['birthdate'])) {
        $date = DateTime::createFromFormat('Y-m-d', $data['birthdate']);
        if (!$date || $date->format('Y-m-d') !== $data['birthdate']) {
            $errors['birthdate'] = 'Введите корректную дату рождения.';
        }
    }
    
    if (empty($data['sex'])) {
        $errors['sex'] = 'Выберите пол.';
    } elseif (!in_array($data['sex'], ['male', 'female'])) {
        $errors['sex'] = 'Некорректное значение пола.';
    }
    
    if (empty($data['languages']) || !is_array($data['languages'])) {
        $errors['languages'] = 'Выберите хотя бы один язык программирования.';
    } else {
        $allowed_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];
        foreach ($data['languages'] as $lang) {
            if (!in_array($lang, $allowed_languages)) {
                $errors['languages'] = 'Выбран некорректный язык программирования.';
                break;
            }
        }
    }
    
    if (!$is_update && empty($data['contract'])) {
        $errors['contract'] = 'Подтвердите согласие с условиями.';
    } elseif (!$is_update && $data['contract'] !== '1' && $data['contract'] !== 1 && $data['contract'] !== true) {
        $errors['contract'] = 'Подтвердите согласие с условиями.';
    }
    
    return $errors;
}
?>