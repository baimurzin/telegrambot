<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

class BotController extends Controller
{

    /**
     * BotController constructor.
     */
    public function __construct()
    {
    }

    public function handle(Request $request) {
        Storage::put('file.test', json_encode($request->input('message')));
    }
}
