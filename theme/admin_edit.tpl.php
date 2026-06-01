<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #f5f5f5; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h1 { color: #ff9900; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 10px; }
        .checkbox-group label { display: flex; align-items: center; gap: 5px; font-weight: normal; }
        .btn { padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn-save { background: #28a745; color: white; }
        .btn-cancel { background: #6c757d; color: white; text-decoration: none; display: inline-block; margin-left: 10px; }
        .error { color: #dc3545; font-size: 14px; margin-top: 5px; }
        .field-error { border-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-edit"></i> Редактирование пользователя #<?php echo $c['user']['id']; ?></h1>
        
        <?php
        $errors = isset($_SESSION['admin_edit_errors']) ? $_SESSION['admin_edit_errors'] : [];
        $form_data = isset($_SESSION['admin_edit_data']) ? $_SESSION['admin_edit_data'] : $c['user'];
        unset($_SESSION['admin_edit_errors'], $_SESSION['admin_edit_data']);
        ?>
        
        <form method="POST" action="?q=admin/<?php echo $c['user']['id']; ?>">
            <input type="hidden" name="_method" value="PUT">
            
            <div class="form-group">
                <label>ФИО *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" class="<?php echo isset($errors['name']) ? 'field-error' : ''; ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="error"><?php echo $errors['name']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Телефон</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Дата рождения</label>
                <input type="date" name="birthdate" value="<?php echo htmlspecialchars($form_data['birthdate'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Пол *</label>
                <select name="sex" class="<?php echo isset($errors['sex']) ? 'field-error' : ''; ?>">
                    <option value="">Выберите</option>
                    <option value="male" <?php echo ($form_data['sex'] ?? '') == 'male' ? 'selected' : ''; ?>>Мужской</option>
                    <option value="female" <?php echo ($form_data['sex'] ?? '') == 'female' ? 'selected' : ''; ?>>Женский</option>
                </select>
                <?php if (isset($errors['sex'])): ?>
                    <div class="error"><?php echo $errors['sex']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Языки программирования</label>
                <div class="checkbox-group">
                    <?php foreach ($c['all_languages'] as $lang): ?>
                        <label>
                            <input type="checkbox" name="languages[]" value="<?php echo $lang; ?>"
                                <?php echo (in_array($lang, (array)($form_data['languages'] ?? []))) ? 'checked' : ''; ?>>
                            <?php echo $lang; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Биография</label>
                <textarea name="biography" rows="5"><?php echo htmlspecialchars($form_data['biography'] ?? ''); ?></textarea>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Сохранить</button>
                <a href="?q=admin" class="btn btn-cancel"><i class="fas fa-times"></i> Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
