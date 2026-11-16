<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AutoDmController extends Controller
{
    /**
     * Display Auto DM rules & automation settings
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $accountsQuery = Account::query();
        if (!$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('name')->get();

        return view('admin.auto_dm.index', compact('accounts'));
    }
}

