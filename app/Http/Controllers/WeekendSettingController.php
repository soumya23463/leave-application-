<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeekendSettingRequest;
use App\Models\WeekendSetting;

class WeekendSettingController extends Controller
{
    /**
     * All users can view weekend settings; only admins can manage them.
     */
    public function index()
    {
        $weekendSettings = WeekendSetting::orderByDesc('effective_date')->paginate(15);

        return view('weekend-settings.index', compact('weekendSettings'));
    }

    public function create()
    {
        abort_unless(isAdmin(), 403);

        return view('weekend-settings.create');
    }

    public function store(WeekendSettingRequest $request)
    {
        abort_unless(isAdmin(), 403);

        WeekendSetting::create($request->validated());

        return redirect()->route('weekend-settings.index')->with('success', 'Weekend setting created.');
    }

    public function show(WeekendSetting $weekendSetting)
    {
        return view('weekend-settings.show', compact('weekendSetting'));
    }

    public function edit(WeekendSetting $weekendSetting)
    {
        abort_unless(isAdmin(), 403);

        return view('weekend-settings.edit', compact('weekendSetting'));
    }

    public function update(WeekendSettingRequest $request, WeekendSetting $weekendSetting)
    {
        abort_unless(isAdmin(), 403);

        $weekendSetting->update($request->validated());

        return redirect()->route('weekend-settings.index')->with('success', 'Weekend setting updated.');
    }

    public function destroy(WeekendSetting $weekendSetting)
    {
        abort_unless(isAdmin(), 403);

        $weekendSetting->delete();

        return redirect()->route('weekend-settings.index')->with('success', 'Weekend setting deleted.');
    }
}
