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

            'registros_por_dia' => $this->getUsersPerDay(),
            'registros_por_semana' => $this->getUsersPerWeek(),
            'registros_por_mes' => $this->getUsersPerMonth()
        ]);
    }

    // Usuarios por día
    private function getUsersPerDay()
    {
        return User::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();
    }

    // Usuarios por semana
    private function getUsersPerWeek()
    {
        return User::select(
            DB::raw('YEAR(created_at) as anio'),
            DB::raw('WEEK(created_at, 1) as semana'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subWeeks(8))
            ->groupBy('anio', 'semana')
            ->orderBy('anio', 'asc')
            ->orderBy('semana', 'asc')
            ->get();
    }


    // Usuarios por mes
    private function getUsersPerMonth()
    {
        return User::select(
            DB::raw('YEAR(created_at) as anio'),
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get();
    }
}
