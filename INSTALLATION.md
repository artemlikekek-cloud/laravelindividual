# Инструкция по установке проекта на новом ПК

## 1. Установка необходимого ПО

### PHP (версия 8.2 или выше)
```bash
# Скачай и установи PHP с официального сайта
# https://windows.php.net/download/
```

### Composer
```bash
# Скачай и установи Composer
# https://getcomposer.org/download/
```

### PostgreSQL (версия 15 или выше)
```bash
# Скачай и установи PostgreSQL
# https://www.postgresql.org/download/windows/
# При установке запомни пароль для пользователя postgres
```

### Git (если еще не установлен)
```bash
# https://git-scm.com/download/win
```

### Node.js и npm (для фронтенда)
```bash
# https://nodejs.org/ (скачай LTS версию)
```

---

## 2. Клонирование проекта

```bash
# Открой PowerShell или командную строку
git clone https://github.com/artemlikekek-cloud/laravelindividual.git
cd laravelindividual
```

---

## 3. Установка зависимостей

### Установи PHP зависимости
```bash
composer install
```

### Установи JavaScript зависимости
```bash
npm install
```

---

## 4. Настройка окружения

### Создай файл .env
```bash
# Скопируй .env.example в .env
copy .env.example .env
```

### Открой .env и настрой базу данных
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sto_db
DB_USERNAME=postgres
DB_PASSWORD=твой_пароль_от_postgres
```

### Сгенерируй ключ приложения
```bash
php artisan key:generate
```

---

## 5. Настройка базы данных PostgreSQL

### Открой pgAdmin или psql и создай базу данных
```sql
CREATE DATABASE sto_db;
```

### Запусти миграции (создание таблиц)
```bash
php artisan migrate
```

### Заполни базу тестовыми данными
```bash
php artisan db:seed
```

---

## 6. Компиляция фронтенда

```bash
# Скомпилируй CSS и JavaScript
npm run build
```

---

## 7. Запуск проекта

```bash
# Запусти сервер разработки
php artisan serve
```

Проект будет доступен по адресу: **http://127.0.0.1:8000**

---

## 8. Вход в админ-панель

- URL: http://127.0.0.1:8000/admin
- Email: **admin@sto.local**
- Пароль: **admin123**

---

## Частые проблемы и решения

### Ошибка "could not find driver"
```bash
# Включи расширения в php.ini
# Найди файл php.ini и раскомментируй (убери точку с запятой):
extension=pdo_pgsql
extension=pgsql
```

### Ошибка подключения к PostgreSQL
```bash
# Проверь, запущен ли PostgreSQL
# Открой Диспетчер задач → Службы → найди postgresql-x64-15
# Если не запущен, запусти службу
```

### Ошибка "NPM command not found"
```bash
# Установи Node.js с официального сайта
# https://nodejs.org/
```

### Порт 8000 уже занят
```bash
# Запусти на другом порту
php artisan serve --port=8080
```

---

## Краткая шпаргалка команд

```bash
# Клонирование
git clone https://github.com/artemlikekek-cloud/laravelindividual.git
cd laravelindividual

# Установка
composer install
npm install
copy .env.example .env
php artisan key:generate

# База данных
php artisan migrate
php artisan db:seed

# Компиляция
npm run build

# Запуск
php artisan serve
```

---

## Системные требования

- **PHP**: 8.2+
- **Composer**: 2.0+
- **PostgreSQL**: 15+
- **Node.js**: 18+
- **npm**: 9+
- **Git**: 2.0+

---

## Структура после установки

```
laravelindividual/
├── app/              # Логика приложения
├── database/         # Миграции и сидеры
├── public/           # Публичные файлы (CSS, JS)
├── resources/        # Шаблоны Blade
├── routes/           # Маршруты
├── .env              # Конфигурация (создается вручную)
└── vendor/           # Зависимости (создается composer install)
```

---

## Проверка работоспособности

1. Открой http://127.0.0.1:8000 - должна открыться главная страница
2. Открой http://127.0.0.1:8000/booking - форма бронирования
3. Открой http://127.0.0.1:8000/admin - вход в админку
4. Войди с admin@sto.local / admin123 - должна открыться панель управления

Если всё работает - проект установлен правильно! ✅
