<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }
        
        // Create a cache key based on the search query
        $cacheKey = 'search_' . md5($query);
        
        // Cache search results for 10 minutes
        return Cache::remember($cacheKey, now()->addMinutes(10), function() use ($query) {
            // Use a more efficient direct query
            $users = DB::table('users')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->where(function($q) use ($query) {
                    $q->where('username', 'LIKE', "{$query}%") // Prefix match is faster
                      ->orWhere('name', 'LIKE', "%{$query}%");
                })
                ->select(
                    'users.id', 
                    'users.username', 
                    'users.name', 
                    'profiles.image',
                    DB::raw("CASE 
                        WHEN username LIKE '{$query}%' THEN 1
                        WHEN name LIKE '{$query}%' THEN 2
                        ELSE 3 
                    END AS relevance")
                )
                ->orderBy('relevance')
                ->limit(5)
                ->get();
            
            // Transform results to include the full profile image URL
            return $users->map(function($user) {
                $image = $user->image ? 'storage/' . $user->image : 'storage/profile/default-avatar.png';
                $user->profile = (object)[
                    'image' => $image
                ];
                $user->profile->image = url($image);
                
                return $user;
            });
        });
    }

    public function index()
    {
        return view('search.index');
    }
}
