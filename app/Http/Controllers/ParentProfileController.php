<?php

namespace App\Http\Controllers;

use App\Models\ParentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentProfileController extends Controller
{
    public function create(): View
    {
        return view('parents.create', [
            'profile' => ParentProfile::where('user_id', auth()->id())->first(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
        ]);

        ParentProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return redirect()->route('dashboard')->with('success', 'Parent profile saved.');
    }
}
