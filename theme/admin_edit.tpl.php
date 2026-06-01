<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя - Админ-панель</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #f5f5f5; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h1 { color: #ff9900; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        .required { color: #dc3545; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 14px; transition: border-color 0.3s; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #ff9900; }
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; }
        .checkbox-group label { display: flex; align-items: center; gap: 6px; font-weight: normal; cursor: pointer; background: #f8f9fa; padding: 6px 12px; border-radius: 20px; transition: background 0.3s; }
        .checkbox-group label:hover { background: #e9ecef; }
        .checkbox-group input[type="checkbox"] { width: auto; margin-right: 5px; }
        .error { color: #dc3545; font-size: 13px; margin-top: 5px; display: block; }
        .field-error { border-color: #dc3545 !important; background: #fff8f8; }
        .btn-group { margin-top: 30px; display: flex; gap: 15px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; font-family: inherit; transition: all 0.3s; }
        .btn-save { background: #28a745; color: white; }
        .btn-save:hover { background: #218838; transform: translateY(-1px); }
        .btn-cancel { background: #6c757d; color: white; text-decoration: none; display: inline-block; text-align: center; }
        .btn-cancel:hover { background: #5a6268; transform: translateY(-1px); }
        hr { margin: 20px 0; border: none; border-top: 1px solid #eee; }
        .info-text { background: #e8f4fd; padding: 10px 15px; border-radius: 8px; font-size: 13px; color: #0c5460; margin-bottom: 20px; }
        @media (max-width: 640px) {
            .container { padding: 20px; }
            .btn-group { flex-direction: column; }
            .btn-cancel { text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-edit"></i> Редактирование пользователя</h1>
        <div class="subtitle">ID: <?php echo $c['user']['id']; ?> | Дата регистрации: <?php echo $c['user']['created_at'] ?? 'не указана'; ?></div>
        
        <?php
        // Получаем ошибки из сессии, если есть
        $errors = isset($_SESSION['admin_edit_errors']) ? $_SESSION['admin_edit_errors'] : [];
        $form_data = isset($_SESSION['admin_edit_data']) ? $_SESSION['admin_edit_data'] : $c['user'];
        // Очищаем сессию после чтения
        unset($_SESSION['admin_edit_errors'], $_SESSION['admin_edit_data']);
        ?>
        
        <?php if (!empty($errors)): ?>
            <div class="info-text" style="background:#f8d7da; color:#721c24;">
                <i class="fas fa-exclamation-triangle"></i> Пожалуйста, исправьте ошибки в форме.
            </div>
        <?php endif; ?>
        
        <!-- ОСНОВНОЕ ИСПРАВЛЕНИЕ: action указывает на URL с ID пользователя -->
        <form method="POST" action="?q=admin/<?php echo $c['user']['id']; ?>">
            <!-- Скрытое поле _method для имитации PUT -->
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="id" value="<?php echo $c['user']['id']; ?>">
            
            <div class="form-group">
                <label>ФИО <span class="required">*</span></label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" 
                       class="<?php echo isset($errors['name']) ? 'field-error' : ''; ?>">
                <?php if (isset($errors['name'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['name']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Телефон</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" 
                       placeholder="+7 (XXX) XXX-XX-XX">
                <?php if (isset($errors['phone'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['phone']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" 
                       placeholder="example@mail.com">
                <?php if (isset($errors['email'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['email']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Дата рождения</label>
                <input type="date" name="birthdate" value="<?php echo htmlspecialchars($form_data['birthdate'] ?? ''); ?>">
                <?php if (isset($errors['birthdate'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['birthdate']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Пол <span class="required">*</span></label>
                <select name="sex" class="<?php echo isset($errors['sex']) ? 'field-error' : ''; ?>">
                    <option value="">Выберите пол</option>
                    <option value="male" <?php echo ($form_data['sex'] ?? '') == 'male' ? 'selected' : ''; ?>>Мужской</option>
                    <option value="female" <?php echo ($form_data['sex'] ?? '') == 'female' ? 'selected' : ''; ?>>Женский</option>
                </select>
                <?php if (isset($errors['sex'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['sex']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Любимые языки программирования</label>
                <div class="checkbox-group">
                    <?php 
                    $user_languages = (array)($form_data['languages'] ?? []);
                    foreach ($c['all_languages'] as $lang): 
                    ?>
                        <label>
                            <input type="checkbox" name="languages[]" value="<?php echo htmlspecialchars($lang); ?>"
                                <?php echo in_array($lang, $user_languages) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($lang); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($errors['languages'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['languages']); ?></span>
                <?php endif; ?>
                <small style="color: #666; display: block; margin-top: 8px;">Выберите один или несколько языков</small>
            </div>
            
            <div class="form-group">
                <label>Биография</label>
                <textarea name="biography" rows="5" placeholder="Расскажите немного о себе..."><?php echo htmlspecialchars($form_data['biography'] ?? ''); ?></textarea>
            </div>
            
            <hr>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Сохранить изменения</button>
                <a href="?q=admin" class="btn btn-cancel"><i class="fas fa-times"></i> Отмена</a>
            </div>
        </form>
    </div>
    
    <?php if (!empty($errors)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const firstError = document.querySelector('.field-error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
