@include('layouts.header')

<div class="container">
    <h2 class="page-title">Онлайн запись на СТО</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="booking-wrapper">
        <form method="POST" action="/booking" id="bookingForm" class="booking-form">
            @csrf

            <!-- Шаг 1: Услуги -->
            <div class="booking-step">
                <div class="step-header">
                    <span class="step-number">1</span>
                    <h3>Выберите услуги</h3>
                </div>
                <div class="services-grid">
                    @foreach($services as $service)
                        <label class="service-card">
                            <input type="checkbox" name="services[]" value="{{ $service->id }}" 
                                   data-duration="{{ $service->duration_minutes }}" 
                                   data-price="{{ $service->price }}"
                                   onchange="calculateTotal()">
                            <div class="service-content">
                                <h4>{{ $service->name }}</h4>
                                <p class="service-desc">{{ $service->description }}</p>
                                <div class="service-meta">
                                    <span class="service-time">⏱️ {{ $service->duration_minutes }} мин</span>
                                    <span class="service-price">{{ number_format($service->price, 0, ',', ' ') }} ₽</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Итого панель -->
            <div class="summary-panel" id="summaryPanel" style="display: none;">
                <div class="summary-content">
                    <span class="summary-label">Выбрано услуг:</span>
                    <span id="selectedCount">0</span>
                </div>
                <div class="summary-content">
                    <span class="summary-label">Время:</span>
                    <span id="totalTime">0</span> мин
                </div>
                <div class="summary-content">
                    <span class="summary-label">Итого:</span>
                    <span class="summary-total"><span id="totalPrice">0</span> ₽</span>
                </div>
            </div>

            <!-- Шаг 2: Дата -->
            <div class="booking-step">
                <div class="step-header">
                    <span class="step-number">2</span>
                    <h3>Выберите дату</h3>
                </div>
                <div class="form-group">
                    <input type="date" name="appointment_date" id="appointmentDate" required 
                           min="{{ date('Y-m-d') }}" 
                           onchange="loadAvailableTimes()" class="date-input">
                </div>
            </div>

            <!-- Шаг 3: Время -->
            <div class="booking-step">
                <div class="step-header">
                    <span class="step-number">3</span>
                    <h3>Выберите время</h3>
                </div>
                <div id="timeSlotsContainer" class="time-slots">
                    <p class="time-hint">Выберите дату и услуги для отображения доступного времени</p>
                </div>
                <input type="hidden" name="appointment_time" id="selectedTime">
            </div>

            <!-- Шаг 4: Данные -->
            <div class="booking-step">
                <div class="step-header">
                    <span class="step-number">4</span>
                    <h3>Ваши данные</h3>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Ваше имя *</label>
                        <input type="text" name="client_name" required value="{{ old('client_name') }}" placeholder="Иван Иванов">
                    </div>

                    <div class="form-group">
                        <label>Телефон *</label>
                        <input type="tel" name="client_phone" required value="{{ old('client_phone') }}" placeholder="+7 (999) 123-45-67">
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="client_email" required value="{{ old('client_email') }}" placeholder="example@mail.ru">
                    </div>

                    <div class="form-group">
                        <label>Марка и модель автомобиля *</label>
                        <input type="text" name="car_model" required value="{{ old('car_model') }}" placeholder="Toyota Camry">
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <span>📝 Записаться на обслуживание</span>
            </button>
        </form>
    </div>
</div>

<script>
function calculateTotal() {
    let totalMinutes = 0;
    let totalPrice = 0;
    let count = 0;
    
    document.querySelectorAll('input[name="services[]"]:checked').forEach(checkbox => {
        totalMinutes += parseInt(checkbox.dataset.duration);
        totalPrice += parseFloat(checkbox.dataset.price);
        count++;
    });
    
    document.getElementById('totalTime').textContent = totalMinutes;
    document.getElementById('totalPrice').textContent = totalPrice.toLocaleString('ru-RU');
    document.getElementById('selectedCount').textContent = count;
    
    const panel = document.getElementById('summaryPanel');
    panel.style.display = count > 0 ? 'flex' : 'none';
    
    loadAvailableTimes();
}

function loadAvailableTimes() {
    const date = document.getElementById('appointmentDate').value;
    const duration = document.getElementById('totalTime').textContent;
    
    if (!date || duration == 0) {
        document.getElementById('timeSlotsContainer').innerHTML = '<p class="time-hint">Выберите дату и услуги</p>';
        return;
    }
    
    document.getElementById('timeSlotsContainer').innerHTML = '<p class="time-hint">Загрузка...</p>';
    
    fetch(`/api/available-times?date=${date}&duration=${duration}`)
        .then(response => response.json())
        .then(times => {
            const container = document.getElementById('timeSlotsContainer');
            
            if (times.length === 0) {
                container.innerHTML = '<p class="time-hint error">На выбранную дату нет свободного времени</p>';
                return;
            }
            
            container.innerHTML = times.map(time => 
                `<button type="button" class="time-slot" onclick="selectTime('${time}')">${time}</button>`
            ).join('');
        });
}

function selectTime(time) {
    document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));
    event.target.classList.add('selected');
    document.getElementById('selectedTime').value = time;
}
</script>

@include('layouts.footer')
