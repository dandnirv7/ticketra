<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $films = Film::select('id', 'judul', 'slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->view('sitemap.index', compact('films'), 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
