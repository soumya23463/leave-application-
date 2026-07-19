<?php

namespace App\Http\Controllers;

use App\Http\Requests\HolidayRequest;
use App\Http\Requests\ImportHolidayRequest;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * All authenticated users can view; write actions are admin-only.
     */
    public function index(Request $request)
    {
        $query = Holiday::orderBy('date', 'asc');

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $holidays = $query->paginate(15)->withQueryString();

        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        abort_unless(isAdmin(), 403);

        return view('holidays.create');
    }

    public function store(HolidayRequest $request)
    {
        Holiday::create($request->validated());

        return redirect()->route('holidays.index')->with('success', 'Holiday created.');
    }

    public function show(Holiday $holiday)
    {
        return view('holidays.show', compact('holiday'));
    }

    public function edit(Holiday $holiday)
    {
        abort_unless(isAdmin(), 403);

        return view('holidays.edit', compact('holiday'));
    }

    public function update(HolidayRequest $request, Holiday $holiday)
    {
        $holiday->update($request->validated());

        return redirect()->route('holidays.index')->with('success', 'Holiday updated.');
    }

    public function destroy(Holiday $holiday)
    {
        abort_unless(isAdmin(), 403);

        $holiday->delete();

        return redirect()->route('holidays.index')->with('success', 'Holiday deleted.');
    }

    /**
     * Stream a sample CSV for the import feature.
     */
    public function sample()
    {
        abort_unless(isAdmin(), 403);

        return response()->streamDownload(function () {
            echo "Date,Occassion\n";
            echo "2026-01-01,New Year\n";
            echo "15/08/2026,Independence Day\n";
        }, 'holidays-sample.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Import holidays from an uploaded spreadsheet.
     */
    public function import(ImportHolidayRequest $request)
    {
        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\HolidayImport, $request->file('file'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        return redirect()->route('holidays.index')->with('success', 'Holidays imported.');
    }
}
