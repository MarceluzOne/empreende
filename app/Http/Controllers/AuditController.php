<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->search, fn($q) => $q->where('description', 'like', '%'.$request->search.'%'))
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Filtro lista apenas funcionários (quem gera ações no painel interno).
        $users = User::where('type', 'funcionario')->orderBy('name')->get(['id', 'name']);

        return view('audit.index', compact('logs', 'users'));
    }
}
