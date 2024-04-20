<?php

namespace App\Http\Controllers;

use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function upload(Request $request)
    {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '유효성 검사 실패',
                'errors' => $validator->errors()
            ], 422);
        }

        // 업로드된 이미지에 대한 배열
        $uploadedImages = [];

        // 만약에 request에 images라는 파일이 존재할 경우
        foreach ($request->file('images') as $image) {
            $path = Storage::disk('s3')->put('reviews/tmp', $image); // 키 값 반환
            $imageUrl = Storage::disk('s3')->url($path); // url 주소 반환
            $uploadedImages[] = [
                'image_url' => $imageUrl,
                'image_key' => $path
            ];
        }

        return response()->json(['images' => $uploadedImages, 'message' => '이미지가 성공적으로 S3에 저장되었습니다. 리뷰 등록을 진행해 주세요.'], 201);
    }

    public function delete(Request $request)
    {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'image_keys.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '유효성 검사 실패',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->input('image_keys') as $imageKey) {
            $reviewImage = ReviewImage::where('image_key', $imageKey)->first();  // ReviewImage 조회

            if (!$reviewImage) {
                return response()->json(['message' => '사진을 찾을 수 없습니다.'], 404);
            }

            // ReviewImage와 연결된 Review를 조회
            $review = $reviewImage->review;

            // 리뷰의 소유자와 현재 사용자를 비교
            if ($review->user_id != Auth::id()) {
                return response()->json([
                    'message' => '리뷰의 소유자만 삭제할 수 있습니다.',
                    'errors' => null
                ], 403);
            }

            // 이미지 삭제
            Storage::disk('s3')->delete($imageKey);
            $reviewImage->delete();
        }

        return response()->json(['message' => '이미지를 성공적으로 삭제하였습니다.'], 200);
    }
}
