<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->query('search'));

        $teams = Team::query()
            ->withCount('employees')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => Team::count(),
            'total_members' => Employee::whereNotNull('team_id')->count(),
        ];

        return view('pages.employees.teams', [
            'title' => 'Data Tim',
            'teams' => $teams,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:teams,name'],
        ], [
            'name.required' => 'Nama tim wajib diisi.',
            'name.unique' => 'Nama tim sudah terdaftar.',
        ]);

        Team::create($validated);

        return redirect()->route('employees.teams')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Tim berhasil ditambahkan.',
            ])
            ->with('toast_type', 'success')
            ->with('toast_message', 'Tim berhasil ditambahkan.');
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:teams,name,' . $team->id],
        ], [
            'name.required' => 'Nama tim wajib diisi.',
            'name.unique' => 'Nama tim sudah terdaftar.',
        ]);

        $team->update($validated);

        return redirect()->route('employees.teams')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Data tim berhasil diperbarui.',
            ])
            ->with('toast_type', 'success')
            ->with('toast_message', 'Data tim berhasil diperbarui.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()->route('employees.teams')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Tim berhasil dihapus.',
            ])
            ->with('toast_type', 'success')
            ->with('toast_message', 'Tim berhasil dihapus.');
    }
}
