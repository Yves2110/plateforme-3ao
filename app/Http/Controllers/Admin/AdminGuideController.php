<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminGuideController extends Controller
{
    public function complete(Request $request): JsonResponse
    {
        $request->user()->forceFill([
            'admin_guide_completed_at' => now(),
        ])->save();

        return response()->json(['ok' => true]);
    }
}
