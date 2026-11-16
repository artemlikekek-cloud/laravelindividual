# 🚗 СТО - Система онлайн-записи

[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Веб-приложение для онлайн-записи в автосервис с административной панелью.

## 📋 Возможности

### Для клиентов:
- 📅 Онлайн-запись на обслуживание
- 🔍 Просмотр услуг и цен
- ⏰ Выбор удобного времени (с проверкой доступности)
- 💬 Оставление отзывов
- 📱 Адаптивный дизайн

### Для администратора:
- 📊 Дашборд с активными записями
- 📁 Архив всех записей
- 🔎 Фильтрация по телефону/email/статусу
- ✅ Управление статусами записей
- ⭐ Модерация отзывов
- 🗑️ Удаление записей

## 🛠️ Технологии

- **Backend:** Laravel 11, PHP 8.4
- **Database:** PostgreSQL 15
- **Frontend:** Blade Templates, Vanilla JavaScript
- **Server:** Nginx + PHP-FPM (в Docker)
- **Patterns:** Repository Pattern, Service Layer, MVC

## ⚡ Быстрый старт

### Требования:
- PHP 8.2+
- Composer
- PostgreSQL 15+
- Git

### Установка:

```bash
# 1. Клонирование репозитория
git clone https://github.com/artemlikekek-cloud/laravelindividual.git
cd laravelindividual

# 2. Установка зависимостей
composer install

# 3. Настройка окружения
copy .env.example .env
# ВАЖНО: Открой .env и укажи пароль от PostgreSQL!
php artisan key:generate

# 4. Создание базы данных в PostgreSQL
# Открой pgAdmin и создай базу sto_db
# ИЛИ через psql: CREATE DATABASE sto_db;

# 5. Миграции и seed данные
php artisan migrate:fresh --seed

# 6. Запуск сервера
php artisan serve
```

Откройте в браузере: **http://127.0.0.1:8000**

## 🔐 Вход в админ-панель

После выполнения `migrate:fresh --seed` будет создан администратор:

```
URL: http://127.0.0.1:8000/admin/login
Email: admin@sto.local
Пароль: admin123
```

## 📚 Документация

- 📖 [Полная инструкция по установке](INSTALLATION.md)
- 🎓 [Руководство для защиты проекта](ЗАЩИТА_ПРОЕКТА.md)

## 🗂️ Структура базы данных

```
users                  → Администраторы
services               → Услуги СТО
appointments           → Записи клиентов
appointment_services   → Связь записей и услуг (Many-to-Many)
working_hours          → График работы
reviews                → Отзывы клиентов
```

## 🏗️ Архитектура

```
app/
├── Contracts/              # Интерфейсы (Repository Pattern)
├── Repositories/           # Реализация работы с БД
├── Services/               # Бизнес-логика
│   ├── AppointmentService  # Создание/управление записями
│   └── TimeSlotService     # Проверка доступности времени
├── Http/Controllers/       # HTTP контроллеры
│   ├── BookingController   # Клиентская часть
│   └── AdminController     # Админ-панель
└── Models/                 # Eloquent модели
```

## 🐳 Запуск через Docker

```bash
# Собрать образ
docker build -t sto-app .

# Запустить контейнер
docker run -p 8080:8080 sto-app
```

Откроется на: **http://localhost:8080**

## 🔧 Настройка базы данных

Проект использует **PostgreSQL** в качестве основной БД.

### Установка PostgreSQL:

1. Скачай и установи: [postgresql.org](https://www.postgresql.org/download/windows/)
2. Запомни пароль для пользователя `postgres`
3. Создай базу данных:

```sql
CREATE DATABASE sto_db;
```

### Настрой `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sto_db
DB_USERNAME=postgres
DB_PASSWORD=ТвойПароль
```

Затем выполни миграции: `php artisan migrate:fresh --seed`

## 🧪 Тестовые данные

После `php artisan migrate:fresh --seed` создаются:

- ✅ 10 услуг СТО (замена масла, шиномонтаж, и т.д.)
- ✅ График работы (Пн-Пт: 9:00-18:00, Сб: 10:00-16:00)
- ✅ 5 тестовых отзывов
- ✅ Администратор (admin@sto.local / admin123)

## 📱 Основные маршруты

| URL | Описание |
|-----|----------|
| `/` | Главная страница |
| `/booking` | Форма записи |
| `/admin/login` | Вход в админ-панель |
| `/admin/dashboard` | Активные записи |
| `/admin/archive` | Архив всех записей |
| `/admin/reviews` | Модерация отзывов |

## 🛡️ Безопасность

- ✅ CSRF защита на всех формах
- ✅ Валидация всех входных данных
- ✅ Bcrypt хеширование паролей
- ✅ Middleware авторизация для админки
- ✅ SQL Injection защита (Eloquent ORM)
- ✅ XSS защита (Blade экранирование)

## 🤝 Участие в разработке

1. Fork проект
2. Создайте feature branch (`git checkout -b feature/amazing-feature`)
3. Commit изменения (`git commit -m 'Add amazing feature'`)
4. Push в branch (`git push origin feature/amazing-feature`)
5. Откройте Pull Request

## 📄 Лицензия

Этот проект лицензирован под [MIT License](LICENSE).

## 👨‍💻 Автор

Создано для индивидуального проекта.

---

**Полная инструкция по установке:** [INSTALLATION.md](INSTALLATION.md)

**Руководство для защиты:** [ЗАЩИТА_ПРОЕКТА.md](ЗАЩИТА_ПРОЕКТА.md)
