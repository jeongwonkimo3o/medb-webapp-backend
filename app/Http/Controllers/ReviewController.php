<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        // 이미지 저장
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $path = Storage::disk('s3')->put('reviews', $image); // 버킷/reviews에 저장

                $imageUrl = Storage::disk('s3')->url($path);
                $reviewImage = new ReviewImage(); // store, update에서만 사용하므로 객체 생성 방식
                $reviewImage->review_id = $review->review_id;
                $reviewImage->image_url = $imageUrl;

                // 키 값(path)를 ReviewImage 모델에 저장
                $reviewImage->image_key = $path;

                $reviewImage->save();
            }
        }

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
            'images.*' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'delete_image_keys.*' => 'sometimes|exists:review_images,image_key',
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $review = $this->reviewModel->where('review_id', $id)->where('user_id', Auth::id())->first();

        // 메모가 없거나 현재 사용자가 소유한 메모가 아닌 경우
        if (!$review) {
            return response()->json([
                'message' => 'Memo not found or permission denied',
                'errors' => null
            ], 403);
        }

        // 이미지 삭제 로직
        if ($request->has('delete_image_keys')) {
            foreach ($request->input('delete_image_keys') as $imageKey) {
                $reviewImage = ReviewImage::where('image_key', $imageKey)->first();
                if ($reviewImage) {
                    // S3에서 이미지 삭제
                    Storage::disk('s3')->delete($reviewImage->image_key);
                    // DB에서 이미지 정보 삭제
                    $reviewImage->delete();
                }
            }
        }

        $review->update($request->all());

        return response()->json(['message' => 'review updated successfully'], 200);
    }

    // 리뷰 삭제
    public function destroy(string $id)
    {
        //
    }
}
