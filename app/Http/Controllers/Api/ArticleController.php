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
            $articles = Article::with('users')->get();

            // Gunakan foreach untuk mengakses setiap artikel
            foreach ($articles as $article) {
                // Tambahkan properti 'imagesUrl' ke setiap artikel
                $article['imageUrl'] = asset('storage/' . $article->image);
            }

            return response()->json([
                'status' => true,
                'message' => __('article.get_success'),
                'data' => $articles,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Count Articles (Token)
     */
    public function countArticles()
    {
        try {
            $articles = Article::with('users')->get();
            $articleCount = $articles->count();

            return response()->json([
                'status' => true,
                'message' => __('article.get_success'),
                'count' => $articleCount,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Read Slug Article
     */
    public function readArticle($slug)
    {
        try {
            $article = Article::with('users')->where('slug', $slug)->first();
            $article->imagesUrl = asset('storage/' . $article->image);

            if (!$article) {
                return response()->json([
                    'status' => false,
                    'message' => __('article.get_fails')
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => __('article.get_success'),
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
        // return response()->json($request->all(), 200);
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

    /**
     * Update Article
     */
    public function update(Request $request, $slug)
    {
        try {
            // Validasi input sesuai kebutuhan Anda
            $request->validate([
                // 'title' => 'required|string|max:255',
                // 'content' => 'required|string',
                // 'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar (opsional).
            ]);

            // Temukan artikel yang akan diperbarui berdasarkan slug.
            $article = Article::where('slug', $slug)->firstOrFail();

            // Perbarui data artikel.
            $article->title = $request->input('title');
            $article->content = $request->input('content');

            // Periksa apakah ada file gambar baru yang diunggah.
            if ($request->hasFile('image')) {
                // Hapus gambar utama dan thumbnail lama jika ada.
                Storage::disk('public')->delete($article->image);
                Storage::disk('public')->delete($article->thumbnail);

                // Simpan gambar utama yang baru.
                $imagePath = $request->file('image')->store('images', 'public');
                $article->image = $imagePath;

                // Buat dan simpan thumbnail yang baru.
                $thumbnail = Image::make(storage_path('app/public/' . $imagePath))
                    ->fit(200, 200)
                    ->encode();
                $thumbnailPath = 'thumbnails/' . time() . '_' . $request->file('image')->getClientOriginalName();
                Storage::disk('public')->put($thumbnailPath, $thumbnail);
                $article->thumbnail = $thumbnailPath;
            }

            // Simpan perubahan dalam database.
            $article->save();

            return response()->json([
                'status' => true,
                'message' => __('article.update_success'),
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
     * Delete Article
     */
    public function destroy($slug)
    {
        try {
            // Temukan artikel berdasarkan slug.
            $article = Article::where('slug', $slug)->firstOrFail();

            // Hapus gambar utama dan thumbnail dari penyimpanan.
            Storage::disk('public')->delete($article->image);
            Storage::disk('public')->delete($article->thumbnail);

            // Hapus artikel dari database.
            $article->delete();

            return response()->json([
                'status' => true,
                'message' => __('article.delete_success'),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }



}
