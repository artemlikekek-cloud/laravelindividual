<?php

namespace App\Http\Controllers;

use App\Contracts\ServiceRepositoryInterface;
use App\Services\AppointmentService;
use App\Services\TimeSlotService;
use App\Models\Review;
use Illuminate\Http\Request;

// Контроллер для работы с записями клиентов
class BookingController extends Controller
{
    public function __construct(
        private ServiceRepositoryInterface $serviceRepository,
        private AppointmentService $appointmentService,
        private TimeSlotService $timeSlotService
    ) {}

    // Главная страница
    public function index()
    {
        $reviews = Review::approved()
            ->latest()
            ->take(10)
            ->get();
        return view('index', compact('reviews'));
    }

    // Сохранение отзыва
    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email:rfc,dns|max:255',
            'review_text' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5'
        ], [
            'client_email.email' => 'Введите корректный email адрес',
            'review_text.min' => 'Отзыв должен содержать минимум 10 символов',
            'rating.between' => 'Оценка должна быть от 1 до 5 звёзд'
        ]);

        Review::create($validated);

        return redirect()->back()->with('success', 'Спасибо за отзыв! Он появится на сайте после модерации.');
    }

    // Страница записи
    public function create()
    {
        $services = $this->serviceRepository->getActive();
        return view('booking', compact('services'));
    }

    // API: Получить доступное время для выбранной даты
    public function getAvailableTimes(Request $request)
    {
        $date = $request->input('date');
        $duration = (int)$request->input('duration', 60);

        $slots = $this->timeSlotService->getAvailableSlots($date, $duration);

        return response()->json($slots);
    }

    // Сохранение записи
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => [
                'required',
                'string',
                'regex:/^(\+7|8)?[\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/'
            ],
            'client_email' => 'required|email:rfc,dns|max:255',
            'car_model' => 'required|string|max:255',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id'
        ], [
            'client_phone.regex' => 'Неверный формат телефона. Используйте формат: +7 (XXX) XXX-XX-XX или 8XXXXXXXXXX',
            'client_email.email' => 'Введите корректный email адрес'
        ]);

        $result = $this->appointmentService->createAppointment($validated);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['appointment_time' => $result['error']]);
        }

        return redirect()->back()->with('success', 'Запись успешно создана! Мы свяжемся с вами для подтверждения.');
    }
}
