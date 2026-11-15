@include('layouts.header')

<div class="container">
    <div class="admin-header">
        <h2>📦 Архив - Все записи</h2>
        <div class="admin-nav">
            <a href="/admin/dashboard" class="btn-active">← Активные</a>
            <a href="/admin/reviews" class="btn-reviews">⭐ Отзывы</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Форма фильтрации -->
    <div class="info-card archive-filters">
        <h3>🔍 Фильтры</h3>
        <form method="GET" action="/admin/archive" class="filter-form">
            <div class="filter-group">
                <label>Телефон</label>
                <input type="text" name="phone" value="{{ request('phone') }}" placeholder="Введите номер...">
            </div>
            
            <div class="filter-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ request('email') }}" placeholder="Введите email...">
            </div>
            
            <div class="filter-group">
                <label>Статус</label>
                <select name="status">
                    <option value="">Все</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Ожидает</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Подтверждена</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершена</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Отменена</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit">Применить</button>
                <a href="/admin/archive">Сбросить</a>
            </div>
        </form>
    </div>

    @if($appointments->isEmpty())
        <p class="reviews-empty">
            @if(request()->has('phone') || request()->has('email') || request()->has('status'))
                По вашему запросу ничего не найдено
            @else
                Записей пока нет
            @endif
        </p>
    @else
        <div class="archive-count">
            Найдено записей: <strong>{{ $appointments->total() }}</strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата/Время</th>
                    <th>Клиент</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Автомобиль</th>
                    <th>Услуги</th>
                    <th>Цена</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->id }}</td>
                        <td>
                            {{ $appointment->appointment_date->format('d.m.Y') }}<br>
                            <strong>{{ substr($appointment->appointment_time, 0, 5) }}</strong>
                        </td>
                        <td>{{ $appointment->client_name }}</td>
                        <td>{{ $appointment->client_phone }}</td>
                        <td><small>{{ $appointment->client_email }}</small></td>
                        <td>{{ $appointment->car_model }}</td>
                        <td>
                            @foreach($appointment->services as $service)
                                <div>• {{ $service->name }}</div>
                            @endforeach
                            <small>({{ $appointment->total_duration_minutes }} мин)</small>
                        </td>
                        <td><strong>{{ number_format($appointment->total_price, 0, ',', ' ') }} ₽</strong></td>
                        <td>
                            <form method="POST" action="/admin/appointments/{{ $appointment->id }}/status">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="badge badge-{{ $appointment->status }} status-select">
                                    <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Ожидает</option>
                                    <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Подтверждена</option>
                                    <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Завершена</option>
                                    <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Отменена</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="/admin/appointments/{{ $appointment->id }}" 
                                  onsubmit="return confirm('Удалить запись?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete-appointment">
                                    Удалить
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $appointments->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@include('layouts.footer')
