<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Get Article
     */
    public function index()
    {
        try {
            $article = Article::with('users')->get();

            return response()->json([
                'status' => true,
                'message' => __('article.create_success'),
                'data' => $article,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Create Article
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Sesuaikan validasi gambar sesuai kebutuhan Anda.
            ]);

            // Simpan gambar utama ke direktori 'images'.
            $imagePath = $request->file('image')->store('images', 'public');

            // Buat thumbnail dan simpan ke direktori 'thumbnails'.
            $thumbnail = Image::make(storage_path('app/public/' . $imagePath))
                            ->fit(200, 200) // Sesuaikan ukuran thumbnail sesuai kebutuhan Anda.
                            ->encode(); // Kompres gambar.

            $thumbnailPath = 'thumbnails/' . time() . '_' . $request->file('image')->getClientOriginalName();
            Storage::put('public/' . $thumbnailPath, $thumbnail);

            // Simpan data artikel ke database.
            $article = new Article([
                'title' => $request->input('title'),
                'slug' => Str::slug(date('Y-m-d').'-'.$request->input('title')),
                'content' => $request->input('content'),
                'image' => $imagePath,
                'thumbnail' => $thumbnailPath,
                'user_id' => auth()->user()->id, // Sesuaikan dengan cara Anda mengelola user saat ini.
            ]);

            $article->save();

            return response()->json([
                'status' => true,
                'message' => __('article.create_success'),
                'data' => $article,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
