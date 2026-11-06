<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
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
        $appointments = Appointment::with('services')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(20);

        return view('admin.dashboard', compact('appointments'));
    }

    // Обновление статуса записи
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = $request->input('status');
        $appointment->save();

        return redirect()->back()->with('success', 'Статус обновлен');
    }

    // Удаление записи
    public function destroy($id)
    {
        Appointment::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Запись удалена');
    }
}
