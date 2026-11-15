@include('layouts.header')

<div class="container">
    <div class="admin-header">
        <h2>📊 Админ-панель - Активные записи</h2>
        <div class="admin-nav">
            <a href="/admin/archive" class="btn-archive">📦 Архив</a>
            <a href="/admin/reviews" class="btn-reviews">⭐ Отзывы</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($appointments->isEmpty())
        <p class="reviews-empty">Активных записей пока нет</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата/Время</th>
                    <th>Клиент</th>
                    <th>Телефон</th>
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
                        <td>
                            {{ $appointment->client_name }}<br>
                            <small>{{ $appointment->client_email }}</small>
                        </td>
                        <td>{{ $appointment->client_phone }}</td>
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
            {{ $appointments->links() }}
        </div>
    @endif
</div>

@include('layouts.footer')
