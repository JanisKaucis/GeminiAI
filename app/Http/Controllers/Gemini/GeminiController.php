<?php

namespace App\Http\Controllers\Gemini;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeminiController extends Controller
{
    public function index()
    {
        return inertia('gemini.Index');
    }
}
