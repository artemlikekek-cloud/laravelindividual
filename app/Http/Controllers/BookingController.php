<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Appointment;
use App\Models\WorkingHour;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Главная страница
    public function index()
    {
        return view('index');
    }

    // Страница записи
    public function create()
    {
        $services = Service::where('is_active', true)->get();
        return view('booking', compact('services'));
    }

    // API: Получить доступное время для выбранной даты
    public function getAvailableTimes(Request $request)
    {
        $date = $request->input('date');
        $duration = (int)$request->input('duration', 60);

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $workingHour = WorkingHour::where('day_of_week', $dayOfWeek)
            ->where('is_working', true)
            ->first();

        if (!$workingHour) {
            return response()->json([]);
        }

        // Получаем все записи на эту дату - используем whereDate!
        $existingAppointments = Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        // Генерируем слоты времени каждые 30 минут
        $slots = [];
        $start = Carbon::parse($date . ' ' . $workingHour->start_time);
        $end = Carbon::parse($date . ' ' . $workingHour->end_time);

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotTime = $start->format('H:i');
            $slotStart = Carbon::parse($date . ' ' . $slotTime);
            $slotEnd = $slotStart->copy()->addMinutes($duration);
            
            // Проверяем конфликты с существующими записями
            $hasConflict = false;
            foreach ($existingAppointments as $appointment) {
                $appointmentStart = Carbon::parse($appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->appointment_time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->total_duration_minutes);
                
                // Проверяем пересечение временных интервалов
                if ($slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart)) {
                    $hasConflict = true;
                    break;
                }
            }

            if (!$hasConflict) {
                $slots[] = $slotTime;
            }

            $start->addMinutes(30);
        }

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
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id'
        ]);

        // Получаем выбранные услуги
        $services = Service::whereIn('id', $validated['services'])->get();
        $totalDuration = $services->sum('duration_minutes');
        $totalPrice = $services->sum('price');

        // ВАЖНО: Проверяем конфликты ПЕРЕД созданием записи
        $date = $validated['appointment_date'];
        $time = $validated['appointment_time'];
        
        $newStart = Carbon::parse($date . ' ' . $time);
        $newEnd = $newStart->copy()->addMinutes($totalDuration);

        // Получаем все подтвержденные и ожидающие записи на эту дату
        // ВАЖНО: whereDate правильно сравнивает даты
        $existingAppointments = Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        \Log::info('Checking conflicts', [
            'date' => $date,
            'time' => $time,
            'new_start' => $newStart->format('Y-m-d H:i'),
            'new_end' => $newEnd->format('Y-m-d H:i'),
            'existing_count' => $existingAppointments->count(),
        ]);

        foreach ($existingAppointments as $existing) {
            $existingStart = Carbon::parse($existing->appointment_date->format('Y-m-d') . ' ' . $existing->appointment_time);
            $existingEnd = $existingStart->copy()->addMinutes($existing->total_duration_minutes);
            
            \Log::info('Checking appointment', [
                'id' => $existing->id,
                'existing_start' => $existingStart->format('Y-m-d H:i'),
                'existing_end' => $existingEnd->format('Y-m-d H:i'),
            ]);
            
            // Есть конфликт если интервалы пересекаются
            if ($newStart->lt($existingEnd) && $newEnd->gt($existingStart)) {
                \Log::warning('CONFLICT DETECTED!', [
                    'new' => [$newStart->format('H:i'), $newEnd->format('H:i')],
                    'existing' => [$existingStart->format('H:i'), $existingEnd->format('H:i')]
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['appointment_time' => 'К сожалению, выбранное время уже занято. Пожалуйста, выберите другое время.']);
            }
        }

        // Создаем запись только если нет конфликтов
        $appointment = Appointment::create([
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'client_email' => $validated['client_email'],
            'car_model' => $validated['car_model'],
            'appointment_date' => $date,
            'appointment_time' => $time,
            'total_duration_minutes' => $totalDuration,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        // Привязываем услуги
        $appointment->services()->attach($validated['services']);

        return redirect()->back()->with('success', 'Запись успешно создана! Мы свяжемся с вами для подтверждения.');
    }
}
