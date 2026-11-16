# 🚀 Инструкция по установке проекта СТО

## 📋 Требования

Перед началом убедись, что установлено:
- ✅ **PHP 8.2+** ([скачать](https://windows.php.net/download/))
- ✅ **Composer** ([скачать](https://getcomposer.org/download/))
- ✅ **PostgreSQL 15+** ([скачать](https://www.postgresql.org/download/windows/))
- ✅ **Git** ([скачать](https://git-scm.com/download/win))
- ✅ **Node.js** (опционально, для фронтенда) ([скачать](https://nodejs.org/))

---

## 📥 Шаг 1: Клонирование репозитория

Открой **PowerShell** или **Командную строку** и выполни:

```bash
# Перейди в нужную папку (например, на рабочий стол)
cd C:\Users\ИМЯ_ПОЛЬЗОВАТЕЛЯ\Desktop

# Клонируй репозиторий
git clone https://github.com/artemlikekek-cloud/laravelindividual.git

# Перейди в папку проекта
cd laravelindividual
```

**Пример:**
```bash
cd C:\Users\Tema\Desktop
git clone https://github.com/artemlikekek-cloud/laravelindividual.git
cd laravelindividual
```

---

## 📦 Шаг 2: Установка зависимостей

```bash
# Установить PHP зависимости через Composer
composer install
```

Если нужен фронтенд (если есть package.json):
```bash
npm install
npm run build
```

---

## ⚙️ Шаг 3: Настройка окружения (.env)

### 3.1 Скопировать файл конфигурации

```bash
# Скопировать .env.example в .env
copy .env.example .env
```

Или если .env.example нет:
```bash
# Создай файл .env вручную
notepad .env
```

### 3.2 Настроить .env файл

Открой файл `.env` и **ОБЯЗАТЕЛЬНО** укажи пароль от PostgreSQL:

```env
# === ОСНОВНЫЕ НАСТРОЙКИ ===
APP_NAME="СТО Онлайн-запись"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# === БАЗА ДАННЫХ PostgreSQL ===
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sto_db
DB_USERNAME=postgres
DB_PASSWORD=ТвойПарольОтPostgreSQL

# === СЕССИИ ===
SESSION_DRIVER=database
SESSION_LIFETIME=120

# === ДРУГИЕ НАСТРОЙКИ ===
CACHE_STORE=database
QUEUE_CONNECTION=database
MAIL_MAILER=log

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

LOG_CHANNEL=stack
LOG_LEVEL=debug
```

**Важно:** Замени `ТвойПарольОтPostgreSQL` на реальный пароль, который ты указал при установке PostgreSQL!

---

## 🔑 Шаг 4: Генерация ключа приложения

```bash
php artisan key:generate
```

Эта команда автоматически добавит `APP_KEY` в `.env` файл.

---

## 🗄️ Шаг 5: Настройка базы данных PostgreSQL

### 5.1 Установить PostgreSQL

Скачай и установи PostgreSQL 15+ с официального сайта:
**https://www.postgresql.org/download/windows/**

При установке:
- ✅ Запомни пароль для пользователя `postgres`
- ✅ Порт оставь по умолчанию: `5432`

### 5.2 Создать базу данных

**Вариант 1: Через pgAdmin (GUI)**
1. Открой **pgAdmin** (установился вместе с PostgreSQL)
2. Подключись к серверу (введи пароль от `postgres`)
3. Правой кнопкой на "Databases" → Create → Database
4. Имя базы: `sto_db`
5. Нажми Save

**Вариант 2: Через командную строку**
```bash
# Открой PowerShell и выполни:
psql -U postgres

# Введи пароль, затем в консоли psql:
CREATE DATABASE sto_db;

# Выход:
\q
```

### 5.3 Запустить миграции и заполнить данными

```bash
# Создать таблицы и заполнить тестовыми данными
php artisan migrate:fresh --seed
```

**Что это делает:**
- ✅ Создает все таблицы (users, services, appointments, reviews, и т.д.)
- ✅ Заполняет услуги СТО
- ✅ Создает график работы
- ✅ Добавляет тестовые отзывы
- ✅ Создает админа (см. ниже)

---

## 👤 Шаг 6: Данные для входа

После выполнения `php artisan migrate:fresh --seed` будет создан **администратор**:

### Проверь файл `database/seeders/DatabaseSeeder.php`:

Должно быть что-то вроде:

```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password')
]);
```

### 🔐 Данные для входа в админ-панель:

```
Email: admin@sto.local
Пароль: admin123
```

**URL админ-панели:** http://localhost:8000/admin/login

---

## 🚀 Шаг 7: Запуск проекта

```bash
# Запустить встроенный сервер Laravel
php artisan serve
```

Сервер запустится на: **http://127.0.0.1:8000**

### Открой в браузере:
- **Главная страница:** http://127.0.0.1:8000
- **Форма записи:** http://127.0.0.1:8000/booking
- **Админ-панель:** http://127.0.0.1:8000/admin/login

---

## ✅ Полная последовательность команд

Скопируй и выполни все команды по порядку:

```bash
# 1. Клонирование
git clone https://github.com/artemlikekek-cloud/laravelindividual.git
cd laravelindividual

# 2. Установка зависимостей
composer install

# 3. Настройка окружения
copy .env.example .env
# ВАЖНО: Открой .env и укажи пароль от PostgreSQL!

# 4. Генерация ключа
php artisan key:generate

# 5. Создание БД в PostgreSQL
# Открой pgAdmin и создай базу sto_db
# ИЛИ выполни: psql -U postgres -c "CREATE DATABASE sto_db;"

# 6. Миграции
php artisan migrate:fresh --seed

# 7. Запуск сервера
php artisan serve
```

---

## 🐳 Альтернатива: Запуск через Docker

Если есть **Docker Desktop**, можно запустить проект в контейнере:

```bash
# Собрать образ
docker build -t sto-app .

# Запустить контейнер
docker run -p 8080:8080 sto-app
```

Проект будет доступен на: **http://localhost:8080**

---

## 🛠️ Дополнительные команды

### Очистка кеша (если что-то не работает)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Пересоздать БД с нуля

```bash
php artisan migrate:fresh --seed
```

### Создать нового администратора вручную

```bash
php artisan tinker
```

Затем в консоли tinker:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@sto.com',
    'password' => Hash::make('qwerty123')
]);

exit
```

---

## 📱 Проверка установки

После запуска проверь, что работает:

- ✅ **Главная страница** показывает отзывы
- ✅ **Форма записи** (/booking) открывается
- ✅ **Выбор даты и времени** работает
- ✅ **Админ-панель** (/admin/login) открывается
- ✅ **Вход в админку** с `admin@sto.local` / `admin123`

---

## ❗ Возможные проблемы

### Ошибка: "Class 'PDO' not found" или "could not find driver"

**Решение:** Включи расширения PostgreSQL в `php.ini`:

1. Найди файл `php.ini` (обычно в `C:\php\php.ini`)
2. Раскомментируй строки (убери точку с запятой):
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   extension=pdo_sqlite
   extension=sqlite3
   ```
3. Перезапусти сервер

### Ошибка: "Permission denied" при создании database.sqlite

**Решение:** Запусти PowerShell **от имени администратора**.

### Ошибка: "No application encryption key has been specified"

**Решение:**
```bash
php artisan key:generate
```

### Порт 8000 уже занят

**Решение:** Укажи другой порт:
```bash
php artisan serve --port=8080
```

---

## 📚 Структура проекта

```
project/
├── app/                    # Основной код приложения
│   ├── Http/Controllers/   # Контроллеры
│   ├── Models/             # Модели БД
│   ├── Services/           # Бизнес-логика
│   └── Repositories/       # Работа с БД
├── database/
│   ├── migrations/         # Миграции таблиц
│   └── seeders/            # Тестовые данные
├── resources/views/        # Blade шаблоны
├── routes/web.php          # Маршруты
├── .env                    # Конфигурация (создается вручную)
└── composer.json           # Зависимости PHP
```

---

## 📞 Контакты для вопросов

Если что-то не работает:
1. Проверь `.env` файл (особенно пароль от PostgreSQL!)
2. Убедись, что PostgreSQL запущен
3. Убедись, что база `sto_db` создана
4. Выполни `php artisan migrate:fresh --seed`
5. Проверь, что `php artisan serve` запущен

---

## 🎯 Быстрый старт (TL;DR)

```bash
git clone https://github.com/artemlikekek-cloud/laravelindividual.git
cd laravelindividual
composer install
copy .env.example .env
# ВАЖНО: Открой .env и укажи пароль PostgreSQL!
php artisan key:generate
# Создай БД в pgAdmin: sto_db
php artisan migrate:fresh --seed
php artisan serve
```

Открой: http://127.0.0.1:8000

Админка: http://127.0.0.1:8000/admin/login
- Email: `admin@sto.local`
- Password: `admin123`

---

**Готово! Проект запущен! 🎉**
