# 📚 Полное руководство по проекту "Онлайн-запись на СТО"

## 🎯 О проекте

Веб-приложение на Laravel для онлайн-записи клиентов на автосервис. Клиенты выбирают услуги, дату и время, а администратор управляет записями через панель управления.

---

## 📦 Установка и запуск

### Требования:
- PHP 8.2+
- Composer
- PostgreSQL
- Git

### Шаги установки:

```bash
# 1. Клонировать репозиторий
git clone https://github.com/artemlikekek-cloud/laravelindividual.git
cd laravelindividual

# 2. Установить зависимости
composer install

# 3. Скопировать файл окружения
cp .env.example .env

# 4. Настроить .env файл
# Открыть .env и указать данные БД:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sto_db
DB_USERNAME=postgres
DB_PASSWORD=ваш_пароль

# 5. Сгенерировать ключ приложения
php artisan key:generate

# 6. Создать БД в PostgreSQL
# В psql выполнить: CREATE DATABASE sto_db;

# 7. Запустить миграции и сидеры
php artisan migrate:fresh --seed

# 8. Запустить сервер
php artisan serve
```

### Открыть в браузере:
- Сайт: http://localhost:8000
- Админка: http://localhost:8000/admin
- Логин: admin@sto.local
- Пароль: admin123

---

## 🏗️ Структура проекта

```
project/
├── app/
│   ├── Contracts/                      # Интерфейсы (абстракции)
│   │   ├── AppointmentRepositoryInterface.php
│   │   ├── ServiceRepositoryInterface.php
│   │   └── WorkingHourRepositoryInterface.php
│   │
│   ├── Http/Controllers/               # Контроллеры (HTTP запросы)
│   │   ├── AdminController.php        # Админ-панель
│   │   └── BookingController.php      # Форма записи
│   │
│   ├── Models/                         # Модели (таблицы БД)
│   │   ├── Appointment.php            # Записи клиентов
│   │   ├── Service.php                # Услуги
│   │   ├── User.php                   # Администраторы
│   │   └── WorkingHour.php            # График работы
│   │
│   ├── Repositories/                   # Репозитории (запросы к БД)
│   │   ├── AppointmentRepository.php
│   │   ├── ServiceRepository.php
│   │   └── WorkingHourRepository.php
│   │
│   ├── Services/                       # Сервисы (бизнес-логика)
│   │   ├── AppointmentService.php     # Логика записей
│   │   └── TimeSlotService.php        # Логика времени
│   │
│   └── Providers/
│       └── AppServiceProvider.php      # Регистрация зависимостей
│
├── database/
│   ├── migrations/                     # Миграции (структура БД)
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_10_21_075931_create_services_table.php
│   │   ├── 2026_10_21_075941_create_appointments_table.php
│   │   ├── 2026_10_21_075947_create_appointment_services_table.php
│   │   └── 2026_10_21_075948_create_working_hours_table.php
│   │
│   └── seeders/                        # Сидеры (начальные данные)
│       ├── DatabaseSeeder.php         # Главный сидер
│       ├── ServicesSeeder.php         # Услуги
│       └── WorkingHoursSeeder.php     # График работы
│
├── public/
│   └── css/
│       └── style.css                   # Стили сайта
│
├── resources/
│   └── views/                          # Представления (HTML)
│       ├── layouts/
│       │   ├── header.blade.php       # Шапка сайта
│       │   └── footer.blade.php       # Подвал сайта
│       ├── admin/
│       │   ├── dashboard.blade.php    # Админ-панель
│       │   └── login.blade.php        # Форма входа
│       ├── index.blade.php            # Главная страница
│       └── booking.blade.php          # Форма записи
│
├── routes/
│   └── web.php                         # Маршруты (URLs)
│
├── .env                                # Конфигурация окружения
├── REQUIREMENTS.md                     # Требования к проекту
├── SOLID_EXPLANATION.md                # Объяснение SOLID принципов
└── README.md                           # Описание проекта
```

---

## 🗄️ База данных

### Таблицы:

**1. users** - Администраторы
- id, name, email, password, remember_token, timestamps

**2. services** - Услуги СТО
- id, name, duration_minutes, price, description, is_active, timestamps

**3. appointments** - Записи клиентов
- id, client_name, client_phone, client_email, car_model
- appointment_date, appointment_time
- total_duration_minutes, total_price, status, notes, timestamps

**4. appointment_services** - Связующая таблица (Many-to-Many)
- id, appointment_id (FK), service_id (FK), timestamps

**5. working_hours** - График работы СТО
- id, day_of_week (0-6), start_time, end_time, is_working, timestamps

### Связи:

```
appointments ←→ appointment_services ←→ services
   (записи)        (связующая)           (услуги)

Тип: Many-to-Many (Многие ко многим)
```

---

## 🔄 Архитектура (SOLID)

### Слои приложения:

```
Routes → Controllers → Services → Repositories → Models → Database
```

**1. Routes (web.php)**
- Определяет URL адреса
- Связывает URL с контроллерами

**2. Controllers**
- Принимают HTTP запросы
- Валидируют данные
- Возвращают ответы (views/JSON)
- **НЕ содержат** бизнес-логику!

**3. Services**
- Содержат бизнес-логику
- Принимают решения
- Проверяют правила
- Координируют работу

**4. Repositories**
- Работают с БД
- Выполняют SQL запросы
- Возвращают модели/коллекции

**5. Models**
- Представляют таблицы БД
- Определяют отношения
- Приводят типы данных

### Принципы SOLID:

**S - Single Responsibility**
- Контроллер - только HTTP
- Сервис - только логика
- Репозиторий - только БД

**O - Open/Closed**
- Расширение через интерфейсы
- Не меняем старый код

**L - Liskov Substitution**
- Можно заменить репозиторий на другую реализацию

**I - Interface Segregation**
- Маленькие специализированные интерфейсы

**D - Dependency Inversion**
- Зависимость от интерфейсов, а не от классов

---

## 🛣️ Маршруты (URLs)

### Публичные:

```php
GET  /                        → Главная страница
GET  /booking                 → Форма записи
POST /booking                 → Создание записи
GET  /api/available-times     → Свободное время (AJAX)
```

### Административные:

```php
GET  /admin                   → Редирект (login/dashboard)
GET  /admin/login             → Форма входа
POST /admin/login             → Обработка входа
POST /admin/logout            → Выход

# Защищенные (требуют авторизации):
GET  /admin/dashboard                  → Панель управления
POST /admin/appointments/{id}/status   → Изменить статус
DELETE /admin/appointments/{id}        → Удалить запись
```

---

## 📝 Как работает запись клиента

### Пошаговый процесс:

**Шаг 1: Выбор услуг**
```
Клиент выбирает чекбоксы:
☑ Замена масла (45 мин, 1500₽)
☑ Диагностика (30 мин, 1000₽)

JavaScript считает:
Итого: 75 минут, 2500₽
```

**Шаг 2: Выбор даты**
```
Клиент выбирает: 15 мая 2026

JavaScript отправляет AJAX:
GET /api/available-times?date=2026-05-15&duration=75
```

**Шаг 3: Генерация слотов (на сервере)**
```php
1. Узнаем день недели (15 мая = пятница)
2. Получаем график: 9:00 - 18:00
3. Получаем занятые записи
4. Генерируем слоты каждые 30 минут
5. Проверяем конфликты
6. Возвращаем: ["9:00", "10:00", "14:00", "15:00"]
```

**Шаг 4: Выбор времени**
```
Клиент кликает: 14:00
JavaScript сохраняет в скрытое поле
```

**Шаг 5: Заполнение данных**
```
Имя: Иван Иванов
Телефон: +7 999 123-45-67
Email: ivan@mail.ru
Машина: Toyota Camry
```

**Шаг 6: Отправка формы**
```
POST /booking
```

**Шаг 7: Обработка (на сервере)**
```php
1. Валидация данных
2. Получение услуг из БД
3. Расчет итого (75 мин, 2500₽)
4. ПРОВЕРКА КОНФЛИКТОВ ← Важно!
5. Создание записи в БД
6. Привязка услуг (appointment_services)
7. Возврат сообщения "Успешно!"
```

---

## ⚙️ Ключевые алгоритмы

### 1. Проверка конфликтов времени

**Задача:** Проверить, свободен ли слот 14:00-15:15

**Алгоритм:**
```php
newStart = 14:00
newEnd = 15:15

foreach (существующие_записи as запись) {
    existingStart = запись.время
    existingEnd = запись.время + запись.длительность
    
    // Проверка пересечения:
    if (newStart < existingEnd AND newEnd > existingStart) {
        return КОНФЛИКТ; // Время занято
    }
}

return СВОБОДНО; // Время свободно
```

**Пример:**
```
Существующая: 13:00 ─── 13:45
Новая:                   14:00 ─── 15:15
Проверка: 14:00 < 13:45? НЕТ → Свободно ✅

Существующая: 13:00 ────── 14:30
Новая:                 14:00 ─── 15:15
Проверка: 14:00 < 14:30? ДА → Конфликт ❌
```

### 2. Генерация временных слотов

**Задача:** Показать свободное время на 15 мая для услуг на 75 минут

**Алгоритм:**
```php
1. График: 9:00 - 18:00
2. Генерируем слоты каждые 30 минут:
   9:00, 9:30, 10:00, 10:30 ... 18:00

3. Для каждого слота:
   a) Проверяем, поместится ли услуга до конца рабочего дня
   b) Проверяем конфликт с существующими записями
   c) Если свободно - добавляем в список

4. Возвращаем: [9:00, 10:00, 14:00, 15:00]
```

---

## 🎨 Frontend

### HTML/Blade шаблоны:

**Структура:**
```blade
@include('layouts.header')

<div class="container">
    <!-- Контент страницы -->
</div>

@include('layouts.footer')
```

### CSS (Flexbox):

**Основные компоненты:**
```css
/* Навигация */
nav {
    display: flex;
    gap: 1rem;
}

/* Сетка услуг */
.services-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.service-card {
    flex: 1 1 280px;
    min-width: 280px;
}

/* Адаптивность */
@media (max-width: 768px) {
    .services-grid {
        flex-direction: column;
    }
}
```

### JavaScript:

**Основные функции:**

```javascript
// 1. Расчет итоговой стоимости
function calculateTotal() {
    // Суммирует выбранные услуги
    // Обновляет панель итогов
}

// 2. Загрузка свободного времени
function loadAvailableTimes() {
    // AJAX запрос к серверу
    // Показывает кнопки времени
}

// 3. Выбор времени
function selectTime(time) {
    // Выделяет кнопку
    // Сохраняет в скрытое поле
}
```

---

## 👨‍💼 Админ-панель

### Функции:

**1. Вход в систему**
```
URL: /admin/login
Логин: admin@sto.local
Пароль: admin123
```

**2. Просмотр записей**
```
URL: /admin/dashboard
Показывает:
- ID записи
- Клиент
- Телефон
- Дата и время
- Услуги
- Статус
- Действия
```

**3. Изменение статуса**
```
Статусы:
- pending (ожидает)
- confirmed (подтвержден)
- completed (выполнен)
- cancelled (отменен)
```

**4. Удаление записи**
```
Кнопка "Удалить" → DELETE запрос → Удаление из БД
```

---

## 🔒 Безопасность

### Применены меры:

**1. CSRF защита**
```blade
<form method="POST">
    @csrf  ← Токен защиты
</form>
```

**2. Валидация данных**
```php
$validated = $request->validate([
    'client_email' => 'required|email|max:255',
    // ...
]);
```

**3. Хэширование паролей**
```php
'password' => bcrypt('admin123')
```

**4. Middleware защита**
```php
Route::middleware(['auth'])->group(function () {
    // Только для авторизованных
});
```

**5. SQL инъекции**
```php
// Eloquent ORM защищает автоматически
Appointment::where('id', $id)->first();
```

---

## 🧪 Тестирование

### Ручное тестирование:

**1. Форма записи:**
- [ ] Главная страница открывается
- [ ] Форма записи загружает услуги
- [ ] Выбор услуг обновляет итог
- [ ] Выбор даты загружает время
- [ ] Можно выбрать время
- [ ] Форма отправляется
- [ ] Показывается сообщение об успехе

**2. Проверка конфликтов:**
- [ ] Создать запись на 14:00-15:00
- [ ] Попробовать создать на 14:30-15:30
- [ ] Должна быть ошибка "Время занято"

**3. Админ-панель:**
- [ ] Вход работает с правильным паролем
- [ ] Отклоняет неправильный пароль
- [ ] Показывает список записей
- [ ] Можно изменить статус
- [ ] Можно удалить запись
- [ ] Выход работает

---

## 🐛 Частые проблемы

### Проблема 1: "Connection refused"
**Решение:** PostgreSQL не запущен
```bash
# Windows
pg_ctl -D "C:\Program Files\PostgreSQL\16\data" start
```

### Проблема 2: "Base table or view not found"
**Решение:** Не запущены миграции
```bash
php artisan migrate:fresh --seed
```

### Проблема 3: "No application encryption key"
**Решение:** Не сгенерирован ключ
```bash
php artisan key:generate
```

### Проблема 4: "Class not found"
**Решение:** Не установлены зависимости
```bash
composer install
```

### Проблема 5: Стили не работают
**Решение:** Проверить путь к CSS
```blade
<link rel="stylesheet" href="/css/style.css">
```

---

## 📊 Данные для тестирования

### Админ:
```
Email: admin@sto.local
Пароль: admin123
```

### Услуги (уже созданы):
1. Диагностика двигателя - 30 мин - 1000₽
2. Замена масла - 45 мин - 1500₽
3. Шиномонтаж - 60 мин - 2000₽
4. Развал-схождение - 90 мин - 2500₽
5. Замена тормозных колодок - 120 мин - 3000₽
6. ТО-1 - 90 мин - 5000₽
7. ТО-2 - 180 мин - 8000₽

### График работы:
- Пн-Пт: 9:00 - 18:00
- Суббота: 10:00 - 16:00
- Воскресенье: выходной

---

## 🚀 Развертывание (Production)

### Для продакшена нужно:

**1. Изменить .env:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ваш-домен.ru
```

**2. Оптимизация:**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**3. Настроить веб-сервер (Nginx):**
```nginx
server {
    listen 80;
    server_name ваш-домен.ru;
    root /path/to/project/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

**4. Настроить SSL (HTTPS):**
```bash
certbot --nginx -d ваш-домен.ru
```

---

## 📚 Полезные команды

### Разработка:

```bash
# Запуск сервера
php artisan serve

# Очистка кэша
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Пересоздать БД
php artisan migrate:fresh --seed

# Создать новую миграцию
php artisan make:migration create_table_name

# Создать новый контроллер
php artisan make:controller NameController

# Создать новую модель
php artisan make:model ModelName

# Просмотр маршрутов
php artisan route:list
```

### Git:

```bash
# Клонировать
git clone https://github.com/artemlikekek-cloud/laravelindividual.git

# Проверить статус
git status

# Добавить изменения
git add .

# Создать коммит
git commit -m "Описание изменений"

# Отправить на GitHub
git push origin main
```

---

## 🎓 Для защиты проекта

### Что нужно знать:

**1. Архитектура:**
- Зачем разделение на слои?
- Что такое SOLID и зачем?
- Как работает Dependency Injection?

**2. База данных:**
- Какие таблицы и зачем?
- Что такое Many-to-Many?
- Зачем связующая таблица?

**3. Логика:**
- Как работает проверка конфликтов?
- Как генерируются временные слоты?
- Как считается итоговая стоимость?

**4. Безопасность:**
- Как защищена админ-панель?
- Что такое CSRF?
- Как валидируются данные?

### Типичные вопросы:

**Q: Почему не сделал всё в контроллере?**
A: Контроллер должен быть тонким. Бизнес-логика в сервисах для переиспользования и тестирования.

**Q: Зачем репозитории?**
A: Слой абстракции над БД. Можно легко поменять БД или добавить кэш.

**Q: Как работает проверка времени?**
A: Проверяем пересечение интервалов: `newStart < existingEnd AND newEnd > existingStart`

**Q: Зачем many-to-many?**
A: Одна запись может включать несколько услуг, и одна услуга может быть в разных записях.

---

## 📞 Контакты

- GitHub: https://github.com/artemlikekek-cloud/laravelindividual
- Email: admin@sto.local (для демо)

---

**Проект готов к защите и демонстрации! Удачи! 🚀**
