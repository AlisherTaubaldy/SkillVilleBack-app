<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $review = Review::create([
            'course_id' => $request->course_id,
            'user_id' => Auth::id(),
            'review' => $request->review,
            'rating' => $request->rating
        ]);

        return response()->json([
            'message' => 'Review submitted successfully'
        ]);
    }
}
