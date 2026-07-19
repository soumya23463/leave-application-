<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeekendSettingRequest;
use App\Models\WeekendSetting;

class WeekendSettingController extends Controller
{
    /**
     * The whole resource is admin-only (guarded by middleware on the route).
     */
    public function index()
    {
        $weekendSettings = WeekendSetting::orderByDesc('effective_date')->paginate(15);

        return view('weekend-settings.index', compact('weekendSettings'));
    }

    public function create()
    {
        return view('weekend-settings.create');
    }

    public function store(WeekendSettingRequest $request)
    {
        WeekendSetting::create($request->validated());

        return redirect()->route('weekend-settings.index')->with('success', 'Weekend setting created.');
    }

    public function show(WeekendSetting $weekendSetting)
    {
        return view('weekend-settings.show', compact('weekendSetting'));
    }

    public function edit(WeekendSetting $weekendSetting)
    {
        return view('weekend-settings.edit', compact('weekendSetting'));
    }

    public function update(WeekendSettingRequest $request, WeekendSetting $weekendSetting)
    {
        $weekendSetting->update($request->validated());

        return redirect()->route('weekend-settings.index')->with('success', 'Weekend setting updated.');
    }

    public function destroy(WeekendSetting $weekendSetting)
    {
        $weekendSetting->delete();

        return redirect()->route('weekend-settings.index')->with('success', 'Weekend setting deleted.');
    }
}
