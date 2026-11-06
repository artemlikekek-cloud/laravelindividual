<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Админ-панель</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-container { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.3rem; font-weight: bold; }
        input { width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; background: #007bff; color: #fff; padding: 0.7rem; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #0056b3; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Вход в админ-панель</h2>

        @if($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/admin/login">
            @csrf
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="admin@sto.local">
            </div>

            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" required value="admin123">
            </div>

            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>
