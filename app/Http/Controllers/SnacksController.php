<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SnacksController extends Controller
{
    
    public function index(Request $request): View
    {
        $fnbItems = Snack::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'desc' => $item->desc,
                'price' => (int) $item->price,
                'emoji' => $item->emoji,
                'category' => $item->category,
                'status' => $item->status,
                'popular' => (int) $item->popular,
                'layout' => $item->layout,
            ];
        })->toArray();

        return view('snacks.index', compact('fnbItems'));
    }
}
