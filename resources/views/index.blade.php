@include('layouts.header')

<div class="container">
    <div class="home-wrapper">
        <h2 class="page-title">Добро пожаловать в наш автосервис!</h2>
        
        <div class="info-card">
            <h3>Наши услуги:</h3>
            <ul class="services-list">
                <li>🔧 Диагностика двигателя</li>
                <li>🛢️ Замена масла и фильтров</li>
                <li>🔄 Шиномонтаж и балансировка</li>
                <li>📐 Развал-схождение</li>
                <li>🛑 Замена тормозных колодок</li>
                <li>⚙️ Техническое обслуживание (ТО-1, ТО-2)</li>
            </ul>
        </div>

        <div class="info-card schedule-card">
            <h3>График работы:</h3>
            <p>📅 Понедельник - Пятница: 9:00 - 18:00</p>
            <p>📅 Суббота: 10:00 - 16:00</p>
            <p>❌ Воскресенье: выходной</p>
        </div>

        <div class="cta-wrapper">
            <a href="/booking" class="cta-button">
                📝 Записаться на обслуживание
            </a>
        </div>

        <!-- Секция отзывов -->
        <div class="reviews-section">
            <h2>⭐ Отзывы наших клиентов</h2>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Список отзывов -->
            @if($reviews->isEmpty())
                <p class="reviews-empty">Будьте первым, кто оставит отзыв!</p>
            @else
                <div class="reviews-list">
                    @foreach($reviews as $review)
                        <div class="info-card review-item">
                            <div class="review-header">
                                <strong class="review-author">{{ $review->client_name }}</strong>
                                <span class="review-rating">
                                    @for($i = 0; $i < $review->rating; $i++)⭐@endfor
                                </span>
                            </div>
                            <p class="review-text">{{ $review->review_text }}</p>
                            <small class="review-date">{{ $review->created_at->format('d.m.Y') }}</small>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Форма добавления отзыва -->
            <div class="info-card">
                <h3>Оставить отзыв</h3>
                <form method="POST" action="/reviews" class="review-form">
                    @csrf
                    <div class="review-form-group">
                        <label>Ваше имя *</label>
                        <input type="text" name="client_name" value="{{ old('client_name') }}" required>
                        @error('client_name')
                            <small>{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="review-form-group">
                        <label>Email *</label>
                        <input type="email" name="client_email" value="{{ old('client_email') }}" required>
                        @error('client_email')
                            <small>{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="review-form-group">
                        <label>Оценка *</label>
                        <select name="rating" required>
                            <option value="">Выберите оценку</option>
                            <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 - Отлично)</option>
                            <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (4 - Хорошо)</option>
                            <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>⭐⭐⭐ (3 - Нормально)</option>
                            <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>⭐⭐ (2 - Плохо)</option>
                            <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>⭐ (1 - Ужасно)</option>
                        </select>
                        @error('rating')
                            <small>{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="review-form-group">
                        <label>Ваш отзыв *</label>
                        <textarea name="review_text" rows="4" required>{{ old('review_text') }}</textarea>
                        @error('review_text')
                            <small>{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <button type="submit">Отправить отзыв</button>
                </form>
            </div>
        </div>

        <div class="cta-wrapper">
            <a href="/booking" class="cta-button">
                📝 Записаться на обслуживание
            </a>
        </div>
    </div>
</div>

@include('layouts.footer')
