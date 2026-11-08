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
    </div>
</div>

@include('layouts.footer')
