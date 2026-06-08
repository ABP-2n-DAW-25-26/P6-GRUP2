<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCalendarExceptionController extends Controller
{
    public function index()
    {
        $exceptions = CalendarException::orderBy('day', 'desc')->get();

        return Inertia::render('admin/CalendarExceptions/Index', [
            'exceptions' => $exceptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => ['required', 'date'],
        ]);

        if (CalendarException::whereDate('day', $validated['day'])->exists()) {
            return back()->withErrors(['day' => 'Aquest dia ja és festiu.']);
        }

        CalendarException::create(['day' => $validated['day']]);

        return to_route('calendar-exceptions.index')
            ->with('success', 'Dia festiu afegit correctament.');
    }

    public function destroy(CalendarException $calendarException)
    {
        $calendarException->delete();

        return to_route('calendar-exceptions.index')
            ->with('success', 'Dia festiu eliminat correctament.');
    }
}
