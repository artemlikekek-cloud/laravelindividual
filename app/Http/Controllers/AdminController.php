<?php

namespace App\Http\Controllers;

use App\Services\AppointmentService;
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

    // Дашборд
    public function dashboard()
    {
        $appointments = $this->appointmentService->getAllAppointments(20);
        return view('admin.dashboard', compact('appointments'));
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
