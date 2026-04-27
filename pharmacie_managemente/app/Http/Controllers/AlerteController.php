<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

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
}
