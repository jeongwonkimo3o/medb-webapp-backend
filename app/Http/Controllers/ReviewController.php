<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    private $reviewModel;

    public function __construct(Review $review)
    {
        $this->middleware('auth:api')->except('index', 'show');
        $this->reviewModel = $review;
    }

    // 전체 리뷰 리스트
    public function index()
    {
        $reviews = $this->reviewModel->with(['images', 'feedbacks'])
            ->orderBy('created_at', 'desc') // 최신 날짜 순
            ->paginate(10);

        return response()->json(['reviews' => $reviews], 200);
    }

    // 리뷰 작성
    public function store(Request $request)
    {
        //
    }

    // 특정 id의 리뷰 보기
    public function show(string $id)
    {
        //
    }

    // 리뷰 업데이트
    public function update(Request $request, string $id)
    {
        //
    }

    // 리뷰 삭제
    public function destroy(string $id)
    {
        //
    }
}
