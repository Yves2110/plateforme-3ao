<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunauteController extends Controller
{
    public function index()
    {
        return view('communaute.index');
    }

    public function category(string $category)
    {
        return view('communaute.category', compact('category'));
    }

    public function thread(string $category, string $thread)
    {
        return view('communaute.thread', compact('category', 'thread'));
    }
}
