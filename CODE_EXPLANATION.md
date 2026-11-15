# 📁 Полная логика всех файлов проекта СТО

## Оглавление
1. [Маршруты (Routes)](#1-маршруты-routes)
2. [Контроллеры (Controllers)](#2-контроллеры-controllers)
3. [Сервисы (Services)](#3-сервисы-services)
4. [Репозитории (Repositories)](#4-репозитории-repositories)
5. [Интерфейсы (Contracts)](#5-интерфейсы-contracts)
6. [Модели (Models)](#6-модели-models)
7. [Миграции (Migrations)](#7-миграции-migrations)
8. [Сидеры (Seeders)](#8-сидеры-seeders)
9. [Представления (Views)](#9-представления-views)
10. [CSS стили](#10-css-стили)
11. [JavaScript логика](#11-javascript-логика)
12. [Провайдеры (Providers)](#12-провайдеры-providers)

---

## 1. Маршруты (Routes)

### 📄 `routes/web.php`

**Назначение:** Определяет все URL-адреса сайта и связывает их с контроллерами.

```php
// ПУБЛИЧНЫЕ МАРШРУТЫ (доступны всем)

GET / → BookingController@index
// Главная страница сайта
// Показывает информацию о СТО, услуги, график работы

GET /booking → BookingController@create
// Страница с формой онлайн-записи
// Загружает все активные услуги из БД
// Показывает пошаговую форму записи

POST /booking → BookingController@store
// Обработка отправленной формы записи
// Валидирует данные клиента
// Создает новую запись в БД
// Возвращает сообщение об успехе/ошибке

GET /api/available-times → BookingController@getAvailableTimes
// AJAX API для получения свободного времени
// Принимает: дату и длительность услуг
// Возвращает: массив свободных временных слотов в JSON

// АДМИНСКИЕ МАРШРУТЫ

GET /admin → проверяет авторизацию
// Если админ залогинен → редирект на /admin/dashboard
// Если не залогинен → редирект на /admin/login

GET /admin/login → AdminController@loginForm
// Показывает форму входа для администратора
// Доступна без авторизации

POST /admin/login → AdminController@login
// Обработка формы входа
// Проверяет email и пароль
// При успехе → редирект на /admin/dashboard
// При ошибке → возврат с сообщением об ошибке

POST /admin/logout → AdminController@logout
// Выход из системы
// Удаляет сессию
// Редирект на /admin/login

// ЗАЩИЩЕННЫЕ АДМИНСКИЕ МАРШРУТЫ (требуют авторизации)

GET /admin/dashboard → AdminController@dashboard
// Панель управления администратора
// Показывает все записи с пагинацией
// Требует middleware('auth')

POST /admin/appointments/{id}/status → AdminController@updateStatus
// Изменение статуса записи
// pending → confirmed → completed / cancelled

DELETE /admin/appointments/{id} → AdminController@destroy
// Удаление записи из системы
```

**Зачем нужен этот файл:**
- Связывает URL с кодом
- Определяет какие страницы доступны
- Разделяет публичные и админские маршруты
- Применяет middleware для защиты

---

## 2. Контроллеры (Controllers)

### 📄 `app/Http/Controllers/BookingController.php`

**Назначение:** Обрабатывает HTTP запросы от клиентов (запись на СТО).

**Задача контроллера:** ТОЛЬКО принять запрос и вернуть ответ. Вся логика в сервисах!

```php
class BookingController extends Controller
{
    // Внедрение зависимостей через конструктор
    public function __construct(
        private ServiceRepositoryInterface $serviceRepository,
        private AppointmentService $appointmentService,
        private TimeSlotService $timeSlotService
    ) {}
    
    // МЕТОД 1: Главная страница
    public function index()
    {
        // Просто возвращает view
        // Ничего не делает, просто показывает HTML
        return view('index');
    }
    
    // МЕТОД 2: Форма записи
    public function create()
    {
        // 1. Получает активные услуги через репозиторий
        $services = $this->serviceRepository->getActive();
        
        // 2. Передает услуги в представление
        return view('booking', compact('services'));
    }
    
    // МЕТОД 3: API для получения свободного времени
    public function getAvailableTimes(Request $request)
    {
        // 1. Получает из запроса дату и длительность
        $date = $request->input('date');        // "2026-05-15"
        $duration = $request->input('duration'); // 75 минут
        
        // 2. Просит сервис найти свободные слоты
        $slots = $this->timeSlotService->getAvailableSlots($date, $duration);
        
        // 3. Возвращает JSON: ["9:00", "10:00", "14:00"]
        return response()->json($slots);
    }
    
    // МЕТОД 4: Сохранение записи
    public function store(Request $request)
    {
        // 1. ВАЛИДАЦИЯ - проверка данных
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'required|email|max:255',
            'car_model' => 'required|string|max:255',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id'
        ]);
        
        // 2. ДЕЛЕГИРОВАНИЕ - отдаем работу сервису
        $result = $this->appointmentService->createAppointment($validated);
        
        // 3. ОБРАБОТКА ОТВЕТА
        if (!$result['success']) {
            // Если время занято - возвращаем ошибку
            return redirect()->back()
                ->withInput()
                ->withErrors(['appointment_time' => $result['error']]);
        }
        
        // Если успешно - показываем сообщение
        return redirect()->back()
            ->with('success', 'Запись успешно создана!');
    }
}
```

**Почему так:**
- Контроллер тонкий (не содержит логику)
- Зависит от интерфейсов (DIP принцип)
- Легко тестировать
- Легко заменить сервисы

---

### 📄 `app/Http/Controllers/AdminController.php`

**Назначение:** Обрабатывает админские запросы (вход, управление записями).

```php
class AdminController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {}
    
    // МЕТОД 1: Показать форму входа
    public function loginForm()
    {
        return view('admin.login');
    }
    
    // МЕТОД 2: Обработка входа
    public function login(Request $request)
    {
        // 1. Валидация
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // 2. Попытка авторизации
        if (Auth::attempt($credentials)) {
            // Успех - создаем сессию
            $request->session()->regenerate();
            return redirect('/admin/dashboard');
        }
        
        // Ошибка - возвращаем с сообщением
        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ]);
    }
    
    // МЕТОД 3: Выход
    public function logout(Request $request)
    {
        Auth::logout();                        // Выходим
        $request->session()->invalidate();     // Удаляем сессию
        $request->session()->regenerateToken(); // Новый CSRF токен
        return redirect('/admin/login');
    }
    
    // МЕТОД 4: Панель управления
    public function dashboard()
    {
        // Получаем записи через сервис
        $appointments = $this->appointmentService->getAllAppointments(20);
        return view('admin.dashboard', compact('appointments'));
    }
    
    // МЕТОД 5: Изменить статус
    public function updateStatus(Request $request, $id)
    {
        $this->appointmentService->updateStatus($id, $request->input('status'));
        return redirect()->back()->with('success', 'Статус обновлен');
    }
    
    // МЕТОД 6: Удалить запись
    public function destroy($id)
    {
        $this->appointmentService->deleteAppointment($id);
        return redirect()->back()->with('success', 'Запись удалена');
    }
}
```

**Зачем:**
- Разделение публичной и админской логики
- Защита через middleware
- Работа с сессиями и авторизацией

---

## 3. Сервисы (Services)

### 📄 `app/Services/AppointmentService.php`

**Назначение:** Содержит БИЗНЕС-ЛОГИКУ работы с записями.

**Главная задача:** Думать, проверять, принимать решения.

```php
class AppointmentService
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private ServiceRepositoryInterface $serviceRepository,
        private TimeSlotService $timeSlotService
    ) {}
    
    // ГЛАВНЫЙ МЕТОД: Создание записи
    public function createAppointment(array $data): array
    {
        // ШАГ 1: Получаем выбранные услуги
        // data['services'] = [2, 3] (ID услуг)
        $services = $this->serviceRepository->findByIds($data['services']);
        // Возвращает коллекцию услуг с ценами и длительностью
        
        // ШАГ 2: Считаем итого
        $totalDuration = $services->sum('duration_minutes'); // 75 минут
        $totalPrice = $services->sum('price');               // 2500₽
        
        // ШАГ 3: КРИТИЧЕСКАЯ ПРОВЕРКА - свободно ли время?
        if ($this->timeSlotService->checkConflict(
            $data['appointment_date'], // "2026-05-15"
            $data['appointment_time'],  // "14:00"
            $totalDuration              // 75
        )) {
            // КОНФЛИКТ! Время занято
            return [
                'success' => false,
                'error' => 'Время уже занято'
            ];
        }
        
        // ШАГ 4: Создаем запись в БД
        $appointment = $this->appointmentRepository->create([
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'],
            'client_email' => $data['client_email'],
            'car_model' => $data['car_model'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'total_duration_minutes' => $totalDuration,
            'total_price' => $totalPrice,
            'status' => 'pending' // Новая запись всегда в ожидании
        ]);
        
        // ШАГ 5: Привязываем услуги (many-to-many)
        // Создаются записи в таблице appointment_services
        $this->appointmentRepository->attachServices($appointment, $data['services']);
        
        // ШАГ 6: Возвращаем успех
        return [
            'success' => true,
            'appointment' => $appointment
        ];
    }
}
```

**Почему сервис важен:**
- Единое место для бизнес-логики
- Легко тестировать
- Можно переиспользовать (API, консольные команды)
- Не зависит от HTTP (можно вызвать откуда угодно)

---

### 📄 `app/Services/TimeSlotService.php`

**Назначение:** Работа с временными слотами (генерация и проверка).

**Самая важная логика проекта!**

```php
class TimeSlotService
{
    private const SLOT_INTERVAL_MINUTES = 30; // Слоты каждые 30 минут
    
    // МЕТОД 1: Получить свободные слоты
    public function getAvailableSlots(string $date, int $duration): array
    {
        // ШАГ 1: Узнаем день недели
        // "2026-05-15" (пятница) → dayOfWeek = 5
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        
        // ШАГ 2: Получаем график работы на этот день
        $workingHour = $this->workingHourRepository->getByDayOfWeek($dayOfWeek);
        // Например: { start: "09:00", end: "18:00", is_working: true }
        
        if (!$workingHour) {
            return []; // Выходной день - нет слотов
        }
        
        // ШАГ 3: Получаем ВСЕ записи на эту дату
        $existingAppointments = $this->appointmentRepository->getByDate(
            $date,
            ['pending', 'confirmed'] // Только активные записи
        );
        
        // ШАГ 4: Генерируем свободные слоты
        return $this->generateAvailableSlots(...);
    }
    
    // МЕТОД 2: Генерация слотов (ПРИВАТНЫЙ)
    private function generateAvailableSlots($date, $workingHour, $duration, $existingAppointments): array
    {
        $slots = [];
        
        // Начало и конец рабочего дня
        $start = Carbon::parse($date . ' ' . $workingHour->start_time); // 09:00
        $end = Carbon::parse($date . ' ' . $workingHour->end_time);     // 18:00
        
        // Идем от начала до конца с шагом 30 минут
        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotTime = $start->format('H:i'); // "09:00"
            
            // Проверяем: есть ли конфликт с существующими записями?
            if (!$this->hasConflict($date, $slotTime, $duration, $existingAppointments)) {
                $slots[] = $slotTime; // Добавляем свободный слот
            }
            
            $start->addMinutes(30); // Следующий слот
        }
        
        return $slots; // ["09:00", "09:30", "10:00", ...]
    }
    
    // МЕТОД 3: Проверка конфликта (САМЫЙ ВАЖНЫЙ!)
    private function hasConflict($date, $time, $duration, $existingAppointments): bool
    {
        // Новый слот
        $slotStart = Carbon::parse($date . ' ' . $time);        // 14:00
        $slotEnd = $slotStart->copy()->addMinutes($duration);   // 15:15
        
        // Проверяем каждую существующую запись
        foreach ($existingAppointments as $appointment) {
            // Время существующей записи
            $appointmentStart = Carbon::parse(...);
            $appointmentEnd = $appointmentStart->copy()->addMinutes(...);
            
            // АЛГОРИТМ ПРОВЕРКИ ПЕРЕСЕЧЕНИЯ:
            // Два интервала пересекаются если:
            // slotStart < appointmentEnd && slotEnd > appointmentStart
            
            if ($slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart)) {
                return true; // КОНФЛИКТ!
            }
        }
        
        return false; // Нет конфликтов
    }
}
```

---

## 4. Репозитории (Repositories)

**Назначение:** Работа с базой данных. ТОЛЬКО запросы!

### 📄 `app/Repositories/AppointmentRepository.php`

```php
class AppointmentRepository implements AppointmentRepositoryInterface
{
    // Получить записи на дату
    public function getByDate(string $date, array $statuses = []): Collection
    {
        $query = Appointment::whereDate('appointment_date', $date);
        
        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
        }
        
        return $query->get();
    }
    
    // Создать запись
    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }
    
    // Привязать услуги (many-to-many)
    public function attachServices(Appointment $appointment, array $serviceIds): void
    {
        // Создает записи в appointment_services
        $appointment->services()->attach($serviceIds);
    }
}
```

**Зачем репозитории:**
- Все запросы в одном месте
- Легко поменять БД (SQLite → PostgreSQL → MySQL)
- Легко добавить кэш
- Тестирование с mock-данными

---

## 5. Интерфейсы (Contracts)

**Назначение:** Определяют "контракт" - какие методы должны быть.

### 📄 `app/Contracts/AppointmentRepositoryInterface.php`

```php
interface AppointmentRepositoryInterface
{
    public function getByDate(string $date, array $statuses = []): Collection;
    public function create(array $data): Appointment;
    public function attachServices(Appointment $appointment, array $serviceIds): void;
    public function getAllPaginated(int $perPage = 20);
    public function findOrFail(int $id): Appointment;
    public function updateStatus(int $id, string $status): bool;
    public function delete(int $id): bool;
}
```

**Зачем:**
- DIP принцип (зависимость от абстракции)
- Можно создать несколько реализаций
- Легко тестировать (mock интерфейс)

---

## 6. Модели (Models)

**Назначение:** Представляют таблицы БД как классы PHP.

### 📄 `app/Models/Appointment.php`

```php
class Appointment extends Model
{
    // Поля которые можно массово заполнять
    protected $fillable = [
        'client_name',
        'client_phone',
        'client_email',
        'car_model',
        'appointment_date',
        'appointment_time',
        'total_duration_minutes',
        'total_price',
        'status',
        'notes'
    ];
    
    // Автоматическое приведение типов
    protected $casts = [
        'appointment_date' => 'date',   // Строка → Carbon дата
        'total_price' => 'decimal:2'    // Число → 2 знака после запятой
    ];
    
    // ОТНОШЕНИЕ: Одна запись → много услуг (many-to-many)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_services');
    }
}
```

**Что дает:**
- Eloquent ORM (работа с БД как с объектами)
- Автоматические timestamps (created_at, updated_at)
- Отношения между таблицами
- Приведение типов

---

## 7. Миграции (Migrations)

**Назначение:** Создают структуру таблиц в БД.

### 📄 `create_appointments_table.php`

```php
public function up(): void
{
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->string('client_name');          // Имя клиента
        $table->string('client_phone');         // Телефон
        $table->string('client_email');         // Email
        $table->string('car_model');            // Марка авто
        $table->date('appointment_date');       // Дата записи
        $table->time('appointment_time');       // Время
        $table->integer('total_duration_minutes'); // Общее время
        $table->decimal('total_price', 10, 2);  // Общая цена
        $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
            ->default('pending');                // Статус
        $table->text('notes')->nullable();      // Примечания
        $table->timestamps();
    });
}
```

### 📄 `create_appointment_services_table.php`

```php
public function up(): void
{
    // СВЯЗУЮЩАЯ ТАБЛИЦА для many-to-many
    Schema::create('appointment_services', function (Blueprint $table) {
        $table->id();
        $table->foreignId('appointment_id')
            ->constrained()              // FOREIGN KEY на appointments
            ->onDelete('cascade');       // При удалении записи - удалить связи
        $table->foreignId('service_id')
            ->constrained()              // FOREIGN KEY на services
            ->onDelete('cascade');
        $table->timestamps();
    });
}
```

---

## 8. Сидеры (Seeders)

**Назначение:** Заполняют БД начальными данными.

### 📄 `DatabaseSeeder.php`

```php
public function run(): void
{
    // 1. Создаем админа
    User::create([
        'name' => 'Администратор',
        'email' => 'admin@sto.local',
        'password' => bcrypt('admin123'), // Хэшируем пароль
    ]);
    
    // 2. Запускаем другие сидеры
    $this->call([
        ServicesSeeder::class,
        WorkingHoursSeeder::class,
    ]);
}
```

---

## 9. Представления (Views)

### 📄 `resources/views/booking.blade.php`

**Самое важное представление!**

```blade
<form method="POST" action="/booking">
    @csrf <!-- Защита от CSRF атак -->

    <!-- ШАГ 1: Выбор услуг -->
    @foreach($services as $service)
        <input type="checkbox" 
               name="services[]" 
               value="{{ $service->id }}"
               data-duration="{{ $service->duration_minutes }}"
               onchange="calculateTotal()">
        {{ $service->name }} - {{ $service->price }}₽
    @endforeach

    <!-- ШАГ 2: Выбор даты -->
    <input type="date" name="appointment_date" onchange="loadAvailableTimes()">

    <!-- ШАГ 3: Выбор времени -->
    <div id="timeSlotsContainer"></div>
    
    <!-- ШАГ 4: Данные клиента -->
    <input type="text" name="client_name" required>
    
    <button type="submit">Записаться</button>
</form>
```

---

## 10. CSS стили

### 📄 `public/css/style.css`

**Ключевые моменты:**

```css
/* 1. CSS ПЕРЕМЕННЫЕ */
:root {
    --primary: #2563eb;
    --spacing-md: 1rem;
}

/* 2. FLEXBOX для сеток */
.services-grid {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-lg);
}

.service-card {
    flex: 1 1 280px;
    min-width: 280px;
}

/* 3. АДАПТИВНОСТЬ */
@media (max-width: 768px) {
    .services-grid {
        flex-direction: column;
    }
}
```

---

## 11. JavaScript логика

### В `booking.blade.php`:

```javascript
// ФУНКЦИЯ 1: Расчет итоговой стоимости
function calculateTotal() {
    let totalMinutes = 0;
    let totalPrice = 0;
    
    document.querySelectorAll('input[name="services[]"]:checked').forEach(checkbox => {
        totalMinutes += parseInt(checkbox.dataset.duration);
        totalPrice += parseFloat(checkbox.dataset.price);
    });
    
    document.getElementById('totalTime').textContent = totalMinutes;
    document.getElementById('totalPrice').textContent = totalPrice;
    
    loadAvailableTimes();
}

// ФУНКЦИЯ 2: Загрузка свободного времени
function loadAvailableTimes() {
    const date = document.getElementById('appointmentDate').value;
    const duration = document.getElementById('totalTime').textContent;
    
    // AJAX запрос к API
    fetch(`/api/available-times?date=${date}&duration=${duration}`)
        .then(response => response.json())
        .then(times => {
            // Создаем кнопки для каждого слота
            container.innerHTML = times.map(time => 
                `<button onclick="selectTime('${time}')">${time}</button>`
            ).join('');
        });
}

// ФУНКЦИЯ 3: Выбор времени
function selectTime(time) {
    document.getElementById('selectedTime').value = time;
}
```

---

## 12. Провайдеры (Providers)

### 📄 `app/Providers/AppServiceProvider.php`

**Назначение:** Регистрация зависимостей (Dependency Injection).

```php
public function register(): void
{
    // СВЯЗЫВАЕМ интерфейсы с реализациями
    
    $this->app->bind(
        AppointmentRepositoryInterface::class,
        AppointmentRepository::class
    );
}
```

**Как работает:**

```php
// В контроллере:
public function __construct(
    private AppointmentRepositoryInterface $repo
) {}

// Laravel автоматически:
// 1. Видит что нужен AppointmentRepositoryInterface
// 2. Смотрит в AppServiceProvider
// 3. Находит что нужно создать AppointmentRepository
// 4. Создает его и передает в конструктор
```

---

## 🎓 Чек-лист для защиты

### Можешь объяснить:

**Архитектура:**
- Зачем разделены контроллеры, сервисы, репозитории?
- Что такое Dependency Injection и где применен?
- Зачем нужны интерфейсы?

**База данных:**
- Какие таблицы и зачем?
- Что такое many-to-many и где применено?
- Как работают миграции и сидеры?

**Логика записи:**
- Как работает проверка конфликтов времени?
- Как генерируются временные слоты?
- Как считается итоговая стоимость?

---

**Удачи на защите!** 💪
