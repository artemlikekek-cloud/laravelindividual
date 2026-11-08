# SOLID Принципы в проекте СТО

Этот документ объясняет, как применены принципы SOLID в проекте системы онлайн-записи на СТО.

## 📋 Оглавление
1. [S - Single Responsibility Principle (Принцип единственной ответственности)](#1-s---single-responsibility-principle)
2. [O - Open/Closed Principle (Принцип открытости/закрытости)](#2-o---openclosed-principle)
3. [L - Liskov Substitution Principle (Принцип подстановки Барбары Лисков)](#3-l---liskov-substitution-principle)
4. [I - Interface Segregation Principle (Принцип разделения интерфейса)](#4-i---interface-segregation-principle)
5. [D - Dependency Inversion Principle (Принцип инверсии зависимостей)](#5-d---dependency-inversion-principle)

---

## 1. S - Single Responsibility Principle
**Принцип:** Каждый класс должен иметь только одну причину для изменения.

### ✅ Как применено:

#### **Контроллеры** - отвечают только за обработку HTTP запросов
- `BookingController` - обрабатывает запросы от клиентов
- `AdminController` - обрабатывает запросы админки

```php
class BookingController extends Controller
{
    // Только обработка HTTP запросов, никакой бизнес-логики!
    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        $result = $this->appointmentService->createAppointment($validated);
        // ...
    }
}
```

#### **Сервисы** - содержат бизнес-логику
- `AppointmentService` - логика работы с записями
- `TimeSlotService` - логика работы со временными слотами

```php
class AppointmentService
{
    // Только бизнес-логика создания записи
    public function createAppointment(array $data): array
    {
        // Расчет стоимости, проверка конфликтов, создание записи
    }
}
```

#### **Репозитории** - работают с базой данных
- `AppointmentRepository` - запросы к БД для записей
- `ServiceRepository` - запросы к БД для услуг
- `WorkingHourRepository` - запросы к БД для рабочих часов

```php
class AppointmentRepository implements AppointmentRepositoryInterface
{
    // Только работа с БД
    public function getByDate(string $date, array $statuses = []): Collection
    {
        return Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', $statuses)
            ->get();
    }
}
```

### 🎯 Результат:
- Каждый класс решает только одну задачу
- Изменения в БД не влияют на бизнес-логику
- Изменения в бизнес-логике не влияют на контроллеры

---

## 2. O - Open/Closed Principle
**Принцип:** Классы открыты для расширения, но закрыты для модификации.

### ✅ Как применено:

#### Интерфейсы позволяют расширять функциональность без изменения существующего кода

**Пример:** Можно легко добавить новый тип репозитория без изменения существующего кода

```php
// Существующий репозиторий
class AppointmentRepository implements AppointmentRepositoryInterface
{
    // Работа с обычной БД
}

// Новый репозиторий (расширение) - БЕЗ ИЗМЕНЕНИЯ старого кода
class CachedAppointmentRepository implements AppointmentRepositoryInterface
{
    // Работа с кэшем + БД
}
```

**Пример 2:** Можно добавить новые методы в сервис без изменения контроллера

```php
class TimeSlotService
{
    // Существующий метод
    public function getAvailableSlots(string $date, int $duration): array { }

    // Добавляем новый метод (РАСШИРЕНИЕ) - контроллер НЕ меняется!
    public function getAvailableSlotsForWeek(string $startDate): array { }
}
```

### 🎯 Результат:
- Новая функциональность добавляется через новые классы
- Старый код остается стабильным и протестированным

---

## 3. L - Liskov Substitution Principle
**Принцип:** Объекты подтипов должны заменять объекты супертипов без нарушения работы программы.

### ✅ Как применено:

#### Все репозитории реализуют интерфейсы и взаимозаменяемы

```php
// Интерфейс определяет контракт
interface AppointmentRepositoryInterface
{
    public function getByDate(string $date, array $statuses = []): Collection;
}

// Обычная реализация
class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function getByDate(string $date, array $statuses = []): Collection
    {
        return Appointment::whereDate('appointment_date', $date)->get();
    }
}

// Можно создать альтернативную реализацию
class TestAppointmentRepository implements AppointmentRepositoryInterface
{
    public function getByDate(string $date, array $statuses = []): Collection
    {
        // Тестовые данные вместо реальной БД
        return collect([/* тестовые записи */]);
    }
}

// Сервис работает с ЛЮБОЙ реализацией одинаково!
class AppointmentService
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository
    ) {}
    // AppointmentRepository или TestAppointmentRepository - без разницы!
}
```

### 🎯 Результат:
- Можно заменить любую реализацию на другую
- Легко писать тесты с mock-объектами

---

## 4. I - Interface Segregation Principle
**Принцип:** Клиенты не должны зависеть от методов, которые они не используют.

### ✅ Как применено:

#### Разделили интерфейсы на маленькие и специализированные

**БЫЛО БЫ ПЛОХО** (один большой интерфейс):
```php
// ❌ ПЛОХО - слишком много методов в одном интерфейсе
interface RepositoryInterface
{
    public function getAll();
    public function getById($id);
    public function getByDate($date);
    public function getActive();
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    // И много других методов...
}
```

**СДЕЛАЛИ ХОРОШО** (несколько маленьких интерфейсов):
```php
// ✅ ХОРОШО - каждый интерфейс содержит только нужные методы

// Интерфейс для записей
interface AppointmentRepositoryInterface
{
    public function getByDate(string $date, array $statuses = []): Collection;
    public function create(array $data): Appointment;
    // Только методы, нужные для записей
}

// Интерфейс для услуг
interface ServiceRepositoryInterface
{
    public function getActive(): Collection;
    public function findByIds(array $ids): Collection;
    // Только методы, нужные для услуг
}

// Интерфейс для рабочих часов
interface WorkingHourRepositoryInterface
{
    public function getByDayOfWeek(int $dayOfWeek): ?WorkingHour;
    // Только методы, нужные для рабочих часов
}
```

### 🎯 Результат:
- Каждый класс реализует только нужные ему методы
- Нет "пустых" методов или исключений "метод не поддерживается"

---

## 5. D - Dependency Inversion Principle
**Принцип:** Зависимости должны строиться относительно абстракций, а не деталей.

### ✅ Как применено:

#### Классы зависят от интерфейсов, а не от конкретных реализаций

**БЫЛО БЫ ПЛОХО** (зависимость от конкретного класса):
```php
// ❌ ПЛОХО - зависит от конкретного класса
class BookingController
{
    public function __construct()
    {
        $this->appointmentRepository = new AppointmentRepository(); // Жесткая связь!
    }
}
```

**СДЕЛАЛИ ХОРОШО** (зависимость от абстракции):
```php
// ✅ ХОРОШО - зависит от интерфейса (абстракции)
class BookingController
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private AppointmentService $appointmentService
    ) {}
    // Конкретная реализация инжектится Laravel автоматически
}

// Регистрация в AppServiceProvider
public function register(): void
{
    $this->app->bind(
        AppointmentRepositoryInterface::class,
        AppointmentRepository::class
    );
    // Можно легко заменить на другую реализацию!
}
```

#### Пример инверсии зависимостей на всех уровнях:

```
┌─────────────────────────────────────────────────────┐
│                   Controller                         │
│  (зависит от интерфейсов Service и Repository)      │
└────────────────────┬────────────────────────────────┘
                     │ зависит от ↓
┌────────────────────▼────────────────────────────────┐
│              ServiceInterface                        │
│         (абстракция бизнес-логики)                  │
└────────────────────┬────────────────────────────────┘
                     │ реализуется в ↓
┌────────────────────▼────────────────────────────────┐
│                  Service                             │
│      (зависит от интерфейсов Repository)            │
└────────────────────┬────────────────────────────────┘
                     │ зависит от ↓
┌────────────────────▼────────────────────────────────┐
│           RepositoryInterface                        │
│          (абстракция работы с БД)                   │
└────────────────────┬────────────────────────────────┘
                     │ реализуется в ↓
┌────────────────────▼────────────────────────────────┐
│                Repository                            │
│            (работа с Eloquent)                       │
└─────────────────────────────────────────────────────┘
```

### 🎯 Результат:
- Легко заменить реализацию (например, для тестов)
- Можно менять БД, кэш, API без изменения бизнес-логики
- Код легко тестировать с mock-объектами

---

## 📊 Архитектура проекта

```
┌─────────────────────────────────────────────────────────────┐
│                         HTTP Request                         │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                      КОНТРОЛЛЕРЫ                             │
│         (обработка HTTP, валидация запросов)                │
│    - BookingController                                       │
│    - AdminController                                         │
└────────────────────────┬────────────────────────────────────┘
                         │ вызывает
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                        СЕРВИСЫ                               │
│              (бизнес-логика приложения)                     │
│    - AppointmentService                                      │
│    - TimeSlotService                                         │
└────────────────────────┬────────────────────────────────────┘
                         │ использует
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                 ИНТЕРФЕЙСЫ РЕПОЗИТОРИЕВ                     │
│                    (абстракции)                              │
│    - AppointmentRepositoryInterface                          │
│    - ServiceRepositoryInterface                              │
│    - WorkingHourRepositoryInterface                          │
└────────────────────────┬────────────────────────────────────┘
                         │ реализуются в
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                     РЕПОЗИТОРИИ                              │
│              (работа с базой данных)                        │
│    - AppointmentRepository                                   │
│    - ServiceRepository                                       │
│    - WorkingHourRepository                                   │
└────────────────────────┬────────────────────────────────────┘
                         │ использует
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                        МОДЕЛИ                                │
│               (Eloquent ORM модели)                         │
│    - Appointment                                             │
│    - Service                                                 │
│    - WorkingHour                                             │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                     БАЗА ДАННЫХ                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎓 Преимущества SOLID в этом проекте

### 1. **Легко тестировать**
```php
// Можно создать mock-репозиторий для тестов
$mockRepo = Mockery::mock(AppointmentRepositoryInterface::class);
$service = new AppointmentService($mockRepo, ...);
```

### 2. **Легко заменить БД**
```php
// Меняем SQLite на PostgreSQL - только один файл (Repository)!
class AppointmentRepository implements AppointmentRepositoryInterface
{
    // Меняем только здесь, остальной код не трогаем
}
```

### 3. **Легко добавлять функции**
```php
// Добавляем кэширование - создаем новый репозиторий
class CachedAppointmentRepository implements AppointmentRepositoryInterface
{
    public function getByDate(string $date, array $statuses = []): Collection
    {
        return Cache::remember("appointments.$date", 3600, function() {
            return $this->repository->getByDate($date, $statuses);
        });
    }
}

// В AppServiceProvider меняем одну строку:
$this->app->bind(
    AppointmentRepositoryInterface::class,
    CachedAppointmentRepository::class // Было: AppointmentRepository::class
);
```

### 4. **Код понятен и структурирован**
- Хочешь изменить бизнес-логику? → Иди в `Services`
- Хочешь изменить запросы к БД? → Иди в `Repositories`
- Хочешь изменить HTTP обработку? → Иди в `Controllers`

---

## 📚 Дополнительные материалы

- [SOLID на русском](https://www.youtube.com/watch?v=O2qdVNxmUBo)
- [Laravel Repository Pattern](https://dev.to/carlomigueldy/getting-started-with-repository-pattern-in-laravel-using-inheritance-and-dependency-injection-2ohe)
- [Clean Architecture в Laravel](https://medium.com/@rizkysbw/clean-architecture-with-laravel-960d95ba7ae3)

---

**Создано для проекта системы онлайн-записи на СТО**
