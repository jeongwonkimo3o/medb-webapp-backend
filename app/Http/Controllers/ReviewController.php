<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\ReviewImageService;

class ReviewController extends Controller
{

    private $reviewModel;
    protected $imageService;

    public function __construct(ReviewImageService $imageService, Review $review)
    {
        $this->middleware('auth:api')->except('index', 'show');
        $this->reviewModel = $review;
        $this->imageService = $imageService;

    }

    // 전체 리뷰 리스트
    public function index()
    {
        $reviews = $this->reviewModel->with(['images'])
            ->orderBy('created_at', 'desc') // 최신 날짜 순
            ->paginate(10);

        return response()->json(['reviews' => $reviews], 200);
    }

    // 리뷰 작성
    public function store(Request $request)
    {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'drug_name' => 'required',
            'content' => 'required',
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // 리뷰 저장
        $review = $this->reviewModel;
        $review->fill($request->all()); // reviewModel 참조
        $review->user_id = Auth::id();
        $review->save();

        // 이미지 저장 서비스 사용
        $this->imageService->processImages($request, $review);



        return response()->json(['message' => 'Review and images saved successfully', 'review' => $review], 201);
    }

    // 특정 id의 리뷰 보기
    public function show(string $id)
    {
        $review = $this->reviewModel->with(['images'])
            ->where('review_id', $id)
            ->first();

        if (!$review) {
            return response()->json([
                'message' => 'review not found or permission denied',
                'errors' => null
            ], 403);
        }

        return response()->json(['review' => $review], 200);
    }

    // 리뷰 업데이트
    public function update(Request $request, string $id)
    {
       
        // put으로 요청 보낼 경우 거부
        if ($request->isMethod('put')) {
            return response()->json([
                'message' => 'PUT method is not allowed',
                'errors' => null
            ], 405);
        }

        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'drug_name' => 'sometimes|required',
            'content' => 'sometimes|required',
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $review = $this->reviewModel->where('review_id', $id)->where('user_id', Auth::id())->first();

        // 리뷰가 없거나 현재 사용자가 소유한 메모가 아닌 경우
        if (!$review) {
            return response()->json([
                'message' => 'Review not found or permission denied',
                'errors' => null
            ], 403);
        }

        $review->update($request->all());
        $this->imageService->processImages($request, $review);


        return response()->json(['message' => 'review updated successfully'], 200);
    }

    // 리뷰 삭제
    public function destroy(string $id)
    {
        $review = $this->reviewModel->where('review_id', $id)->where('user_id', Auth::id())->first();

        // 리뷰가 없거나 현재 사용자가 소유한 리뷰가 아닌 경우
        if (!$review) {
            return response()->json([
                'message' => 'Review not found or permission denied',
                'errors' => null
            ], 403);
        }

        // 리뷰 삭제
        $review->delete();

        return response()->json(['message' => 'Review and related images deleted successfully'], 200);
    }
}
