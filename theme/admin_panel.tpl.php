<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель - Управление заявками</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #f5f5f5; padding: 40px 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        h1 { color: #ff9900; margin-bottom: 10px; }
        .stats-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 15px; margin-bottom: 30px; }
        .stats-grid { display: flex; gap: 30px; flex-wrap: wrap; margin-top: 15px; }
        .stat-item { background: rgba(255,255,255,0.2); padding: 15px 25px; border-radius: 10px; }
        .stat-item h3 { font-size: 28px; }
        .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        th { background: #ff9900; color: white; padding: 15px; text-align: left; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f9f9f9; }
        .btn { display: inline-block; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px; margin: 0 3px; cursor: pointer; border: none; }
        .btn-edit { background: #28a745; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-delete:hover { background: #c82333; }
        .btn-edit:hover { background: #218838; }
        .logout-btn { background: #6c757d; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .logout-btn:hover { background: #5a6268; }
        .back-link { margin-left: 20px; color: #ff9900; }
        .lang-stats { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .lang-bars { margin-top: 15px; }
        .lang-bar { margin-bottom: 10px; }
        .lang-bar span { display: inline-block; background: #ff9900; padding: 5px 10px; border-radius: 5px; color: white; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-shield-alt"></i> Админ-панель</h1>
        <p>Управление заявками участников мероприятий</p>
        
        <div style="margin: 20px 0;">
            <a href="?q=form" class="logout-btn"><i class="fas fa-arrow-left"></i> На сайт</a>
            <a href="?q=logout" class="logout-btn" style="background:#dc3545;"><i class="fas fa-sign-out-alt"></i> Выйти</a>
        </div>
        
        <?php if (!empty($c['message'])): ?>
            <div class="message <?php echo $c['message_type']; ?>">
                <?php echo htmlspecialchars($c['message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-card">
            <h2><i class="fas fa-chart-line"></i> Общая статистика</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $c['total_users']; ?></h3>
                    <p>Всего участников</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-code"></i>
                    <h3><?php echo count($c['language_stats']); ?></h3>
                    <p>Языков программирования</p>
                </div>
            </div>
        </div>
        
        <div class="lang-stats">
            <h2><i class="fas fa-chart-simple"></i> Статистика по языкам программирования</h2>
            <div class="lang-bars">
                <?php foreach ($c['language_stats'] as $stat): ?>
                    <div class="lang-bar">
                        <strong><?php echo htmlspecialchars($stat['language']); ?></strong> - 
                        <?php echo $stat['count']; ?> чел.
                        <span style="width: <?php echo min(300, $stat['count'] * 30); ?>px;">&nbsp;</span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($c['language_stats'])): ?>
                    <p>Нет данных</p>
                <?php endif; ?>
            </div>
        </div>
        
        <h2><i class="fas fa-table"></i> Список участников</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Дата рождения</th>
                    <th>Пол</th>
                    <th>Языки</th>
                    <th>Биография</th>
                    <th>Дата регистрации</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($c['users'] as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                    <td><?php echo $user['birthdate'] ?? '-'; ?></td>
                    <td><?php echo $user['sex'] == 'male' ? 'Мужской' : 'Женский'; ?></td>
                    <td><?php echo htmlspecialchars($user['languages'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars(substr($user['biography'] ?? '', 0, 100)); ?></td>
                    <td><?php echo $user['created_at'] ?? '-'; ?></td>
                    <td>
                        <a href="?q=admin/<?php echo $user['id']; ?>/edit" class="btn btn-edit"><i class="fas fa-edit"></i> Ред.</a>
                        <form method="POST" action="?q=admin/<?php echo $user['id']; ?>" style="display:inline;" onsubmit="return confirm('Удалить пользователя?')">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-delete"><i class="fas fa-trash"></i> Уд.</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($c['users'])): ?>
                <tr>
                    <td colspan="10" style="text-align:center;">Нет данных</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
