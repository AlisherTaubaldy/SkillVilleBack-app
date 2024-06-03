<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function get(Request $request)
    {
        $courses = Course::all(['id', 'title', 'description', 'image_url', 'category_id']);

        $simplifiedCourses = $courses->map(function ($course) {
            $reviewCount = Review::where('course_id', $course->id)->count();
            if ($reviewCount == '0'){
                $averageRating = 0.00;
            } else {
                $averageRating = $reviewCount > 0 ? Review::where('course_id', $course->id)->avg('rating') : 0;
            }

            $category = Category::where('id', $course->category_id)->first();

            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'image_url' => $course->image_url,
                'category' => $category->title,
                'rating' => $averageRating,
                'review ' => $reviewCount
            ];
        });

        return $simplifiedCourses;
    }

    public function getCourseDetail($courseId)
    {
        $course = Course::where("id", $courseId)->first();

        $reviewCount = Review::where('course_id', $course->id)->count();

        if ($reviewCount == '0'){
            $averageRating = 0.00;
        } else {
            $averageRating = $reviewCount > 0 ? Review::where('course_id', $course->id)->avg('rating') : 0;
        }
        $category = Category::where('id', $course->category_id)->first();
        $author = User::where("id", $course->author_id)->first();
        $modules = Module::where("course_id", $courseId)->get();

        $moduleList = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'title' => $module->title,
                'description' => $module->description
            ];
        });

        // Create the CourseDetail data class
        $courseDetail = [
            'id' => $course->id,
            'title' => $course->title,
            'image_url' => $course->image_url,
            'description' => $course->description,
            'price' => $course->price,
            'category' => $category->title,
            'author' => $author->name,
            'rating' => $averageRating,
            'review' => $reviewCount,
            'modules' => $moduleList,
        ];

        return response()->json($courseDetail);

    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'image_url' => 'required|string|max:255',
                'price' => 'required|string|max:255',
                'category_id' => 'required|integer|exists:categories,id',
                'modules' => 'required|array',
                'modules.*.title' => 'required|string|max:255',
                'modules.*.content.video_url' => 'required|string|max:255',
                'modules.*.content.text' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($validated) {
                $course = Course::create([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'image_url' => $validated['image_url'],
                    'price' => $validated['price'],
                    'category_id' => $validated['category_id'],
                    'author_id' => Auth::id()//не факт что робит
                ]);

                foreach ($validated['modules'] as $moduleData) {
                    $module = $course->modules()->create([
                        'title' => $moduleData['title'],
                        'description' => "This is a description for this module"
                    ]);

                    $module->contents()->create([
                        'video_url' => $moduleData['content']['video_url'],
                        'text' => $moduleData['content']['text'],
                    ]);
                }
            });

            return response()->json(['message' => 'Course created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
            ]);
        } catch (ValidationException $e){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        if ($request->file('image')) {
            $image = $request->file('image');
            $path = $image->store('images', 'public');

            return response()->json(['url' => Storage::url($path)], 201);
        }

        return response()->json(['error' => 'Image upload failed'], 400);
    }

    public function uploadVideo(Request $request)
    {
        try {
            $request->validate([
                'video' => 'required|mimes:mp4,mov,avi,flv|max:5120000',
                'description' => 'required|string',
            ]);
        } catch (ValidationException $e){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        if ($request->file('video')) {
            $video = $request->file('video');
            $path = $video->store('videos', 'public');

            return response()->json(['url' => Storage::url($path)], 201);
        }

        return response()->json(['error' => 'Video upload failed'], 400);
    }
}
