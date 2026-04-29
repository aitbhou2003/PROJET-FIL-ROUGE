<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class AlerteController extends Controller
{
    //
    public function index()
    {
        $alertes = Notification::with('stock.medicament')
            ->orderByDesc('created_at')
            ->paginate(20);

        $nonLues = Notification::where('is_read', false)->count();

        return view('admin.alertes.index', compact('alertes', 'nonLues'));
    }


    public function dernieresAlertes()
    {
        $alertes = Notification::with('stock.medicament')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $nonLues = Notification::where('is_read', false)->count();

        return response()->json([
            'alertes' => $alertes,
            'non_lues' => $nonLues
        ]);
    }
}
