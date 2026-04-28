<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function query(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}
