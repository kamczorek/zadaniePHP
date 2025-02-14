<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        // Jeśli admin, pobiera wszystkie wizyty
        if (Auth::user()->role === 'admin') {
            $appointments = Appointment::with('user')->get();
        } else {
            $appointments = Appointment::where('user_id', Auth::id())->get();
        }

        return view('dashboard', compact('appointments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_time' => 'required|date|after:now',
            'service' => 'required|min:3|max:100'
        ]);

        Appointment::create([
            'user_id' => Auth::id(),
            'date_time' => $request->date_time,
            'service' => $request->service,
            'status' => 'zaplanowana'
        ]);

        return redirect('/dashboard')->with('success', 'Wizyta została dodana.');
    }

    public function edit($id)
{
    $appointment = Appointment::findOrFail($id);
    return view('edit_appointment', compact('appointment'));
}

public function update(Request $request, $id)
{
    $appointment = Appointment::findOrFail($id);

    $request->validate([
        'date_time' => 'required|date|after:now',
        'service' => 'required|min:3|max:100',
        'status' => 'required|in:zaplanowana,zrealizowana,anulowana'
    ]);

    $appointment->update([
        'date_time' => $request->date_time,
        'service' => $request->service,
        'status' => $request->status,
    ]);

    return redirect()->route('dashboard')->with('success', 'Wizyta została zaktualizowana.');
}

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Tylko admin lub właściciel może usunąć
        if (Auth::user()->role !== 'admin' && $appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $appointment->delete();
        return redirect('/dashboard')->with('success', 'Wizyta została usunięta.');
    }
}
