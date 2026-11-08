<?php

namespace App\Http\Controllers;

use App\Contracts\ServiceRepositoryInterface;
use App\Services\AppointmentService;
use App\Services\TimeSlotService;
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
        return view('index');
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
            'client_phone' => 'required|string|max:20',
            'client_email' => 'required|email|max:255',
            'car_model' => 'required|string|max:255',
            'appointment_date' => 'required|date|after_or_equal:today', // Можно на сегодня
            'appointment_time' => 'required',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id'
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
