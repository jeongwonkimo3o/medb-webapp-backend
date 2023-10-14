<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ReviewImage;

class ReviewImageService
{
  public function processImages(Request $request, $review)
  {

    // 클라이언트가 임시 저장된 이미지의 키값들을 보냈을 경우(임시 저장된 이미지의 키를 전달하는 개념)
    if ($request->has('temporary_image_keys')) {
      foreach ($request->input('temporary_image_keys') as $imageKey) {
        $newPath = str_replace('reviews/tmp', 'reviews', $imageKey); // 키 값 변경(임시 -> 최종으로 옮겨지는 키값)
        Storage::disk('s3')->move($imageKey, $newPath); // 파일 이동 임시 -> 최종

        $reviewImage = new ReviewImage();
        $reviewImage->review_id = $review->review_id;
        $reviewImage->image_url = Storage::disk('s3')->url($newPath);
        $reviewImage->image_key = $newPath;
        $reviewImage->save();
      }
    }
  }
}
