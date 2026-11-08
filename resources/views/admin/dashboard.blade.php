@include('layouts.header')

<div class="container">
    <h2>📊 Админ-панель - Все записи</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($appointments->isEmpty())
        <p style="margin: 2rem 0; text-align: center; color: #666;">Записей пока нет</p>
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
                                <select name="status" onchange="this.form.submit()" 
                                        class="badge badge-{{ $appointment->status }}" 
                                        style="border:none;cursor:pointer;">
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
                                <button type="submit" style="background:#dc3545;padding:0.3rem 0.6rem;font-size:0.85rem;">
                                    Удалить
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 1.5rem;">
            {{ $appointments->links() }}
        </div>
    @endif
</div>

@include('layouts.footer')
