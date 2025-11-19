<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class StatsController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        return response()->json([
            'resumen_actual' => [
                'hoy' => User::whereDate('created_at', Carbon::today())->count(),
                'esta_semana' => User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                'este_mes' => User::whereMonth('created_at', $now->month)->count(),
                'total_historico' => User::count(),
            ],
            'registros_por_dia' => $this->getUsersPerDay()
        ]);
    }

    private function getUsersPerDay()
    {
        return User::select(DB::raw('Date(created_at) as fecha'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();
    }
}
