@include('layouts.header')

<div class="container">
    <div class="admin-header">
        <h2>⭐ Управление отзывами</h2>
        <div class="admin-nav">
            <a href="/admin/dashboard" class="btn-active">← Активные</a>
            <a href="/admin/archive" class="btn-archive">📦 Архив</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($reviews->isEmpty())
        <p class="reviews-empty">Отзывов пока нет</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Клиент</th>
                    <th>Email</th>
                    <th>Оценка</th>
                    <th>Отзыв</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                    <tr class="{{ $review->is_approved ? 'review-row-approved' : 'review-row-pending' }}">
                        <td>{{ $review->id }}</td>
                        <td>{{ $review->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $review->client_name }}</td>
                        <td><small>{{ $review->client_email }}</small></td>
                        <td class="review-rating">
                            @for($i = 0; $i < $review->rating; $i++)⭐@endfor
                        </td>
                        <td class="review-cell-text">
                            <div>{{ $review->review_text }}</div>
                        </td>
                        <td>
                            @if($review->is_approved)
                                <span class="review-status-approved">✓ Одобрен</span>
                            @else
                                <span class="review-status-pending">⏳ На модерации</span>
                            @endif
                        </td>
                        <td>
                            <div class="review-actions">
                                <form method="POST" action="/admin/reviews/{{ $review->id }}/approve">
                                    @csrf
                                    <button type="submit" class="{{ $review->is_approved ? 'btn-hide' : 'btn-approve' }}">
                                        {{ $review->is_approved ? 'Скрыть' : 'Одобрить' }}
                                    </button>
                                </form>
                                
                                <form method="POST" action="/admin/reviews/{{ $review->id }}" 
                                      onsubmit="return confirm('Удалить отзыв?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-review">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $reviews->links() }}
        </div>
    @endif
</div>

@include('layouts.footer')
