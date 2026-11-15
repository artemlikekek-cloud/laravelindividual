<?php

namespace App\Http\Controllers;

use App\Services\AppointmentService;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Контроллер для административной панели
class AdminController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {}

    // Страница входа
    public function loginForm()
    {
        return view('admin.login');
    }

    // Обработка входа
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ]);
    }

    // Выход
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }

    // Дашборд - только активные заказы
    public function dashboard()
    {
        $appointments = $this->appointmentService->getActiveAppointments(20);
        return view('admin.dashboard', compact('appointments'));
    }

    // Архив - все заказы с фильтрацией
    public function archive(Request $request)
    {
        $query = $this->appointmentService->getAllAppointmentsQuery();
        
        // Фильтр по телефону
        if ($request->filled('phone')) {
            $query->where('client_phone', 'LIKE', '%' . $request->phone . '%');
        }
        
        // Фильтр по email
        if ($request->filled('email')) {
            $query->where('client_email', 'LIKE', '%' . $request->email . '%');
        }
        
        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->latest()->paginate(20);
        
        return view('admin.archive', compact('appointments'));
    }

    // Управление отзывами
    public function reviews()
    {
        $reviews = Review::latest()->paginate(20);
        return view('admin.reviews', compact('reviews'));
    }

    // Одобрить отзыв
    public function approveReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => !$review->is_approved]);
        return redirect()->back()->with('success', 'Статус отзыва обновлен');
    }

    // Удалить отзыв
    public function deleteReview($id)
    {
        Review::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Отзыв удален');
    }

    // Обновление статуса записи
    public function updateStatus(Request $request, $id)
    {
        $this->appointmentService->updateStatus($id, $request->input('status'));
        return redirect()->back()->with('success', 'Статус обновлен');
    }

    // Удаление записи
    public function destroy($id)
    {
        $this->appointmentService->deleteAppointment($id);
        return redirect()->back()->with('success', 'Запись удалена');
    }
}
