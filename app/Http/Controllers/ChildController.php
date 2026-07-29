<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ParentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildController extends Controller
{
    public function index(): View
    {
        $parentProfile = ParentProfile::where('user_id', auth()->id())->firstOrFail();

        return view('children.index', [
            'children' => $parentProfile->children()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('children.create');
    }

    public function edit(Child $child): View
    {
        abort_unless($child->parentProfile->user_id === auth()->id(), 403);

        return view('children.edit', compact('child'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_number' => ['required', 'string', 'max:50', 'unique:children,patient_number'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'sex' => ['required', 'in:male,female'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'medical_conditions' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $parentProfile = ParentProfile::where('user_id', auth()->id())->firstOrFail();

        $parentProfile->children()->create($validated);

        return redirect()->route('children.index')->with('success', 'Child record created.');
    }

    public function update(Request $request, Child $child): RedirectResponse
    {
        abort_unless($child->parentProfile->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'patient_number' => ['required', 'string', 'max:50', 'unique:children,patient_number,' . $child->id],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'sex' => ['required', 'in:male,female'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'medical_conditions' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $child->update($validated);

        return redirect()->route('children.index')->with('success', 'Child record updated.');
    }

    public function destroy(Child $child): RedirectResponse
    {
        abort_unless($child->parentProfile->user_id === auth()->id(), 403);

        $child->delete();

        return redirect()->route('children.index')->with('success', 'Child record deleted.');
    }
}
