<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>СТО - Онлайн запись</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header>
    <h1>🚗 СТО - Станция технического обслуживания</h1>
    <nav>
        <a href="/">Главная</a>
        <a href="/booking">Записаться</a>
        @if(auth()->check())
            <a href="/admin/dashboard">Админ-панель</a>
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:transparent;border:none;color:var(--text-secondary);cursor:pointer;padding:var(--spacing-sm) var(--spacing-md);font-weight:500;border-radius:var(--radius-md);transition:var(--transition);">Выход</button>
            </form>
        @endif
    </nav>
</header>
