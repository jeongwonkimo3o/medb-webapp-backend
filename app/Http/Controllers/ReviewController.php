<?php

namespace App\Http\Controllers;

use App\Models\Drug;
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
        $this->middleware('auth:api')->except('show');
        $this->reviewModel = $review;
        $this->imageService = $imageService;
    }

    // 유저의 리뷰 목록 조회
    public function index()
    {
        $reviews = $this->reviewModel->with(['images', 'drug']) 
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json(['reviews' => $reviews, 'message' => '리뷰 목록 조회 성공'], 200);
    }




    public function show(Request $request, $item_seq)
    {
        // 페이지 파라미터 유효성 검사
        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '유효성 검사 실패',
                'errors' => $validator->errors()
            ], 422);
        }

        // item_seq를 경로 파라미터로 받아 해당하는 drug 찾기
        $drug = Drug::where('item_seq', $item_seq)->first();

        if (!$drug) {
            return response()->json(['message' => '약 정보를 찾을 수 없습니다.'], 404);
        }

        // 해당 drug_id를 가지고 있는 리뷰들을 페이지네이션하여 조회
        $reviews = $drug->reviews()->with(['images'])->orderBy('created_at', 'desc')->paginate(10);

        return response()->json(['reviews' => $reviews, 'message' => '리뷰가 조회되었습니다.'], 200);
    }



    // 리뷰 작성
    public function store(Request $request)
    {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'rating' => 'required|in:좋아요,보통이에요,별로예요',
            'item_seq' => 'required|exists:drugs,item_seq',
            'image_keys' => 'nullable|array', // 이미지 키들이 배열 형태로 전달되었는지 확인
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $drug = Drug::where('item_seq', $request->item_seq)->firstOrFail();

        $review = new Review;
        $review->fill($request->only(['content', 'rating']));
        $review->drug_id = $drug->id;
        $review->user_id = Auth::id();
        $review->save();

        // 이미지 키 배열을 이용하여 ReviewImage 인스턴스 생성
        if ($request->has('image_keys')) {
            foreach ($request->image_keys as $imageKey) {
                $imageUrl = env('S3_BUCKET_URL') . $imageKey; // 환경 변수 사용
                ReviewImage::create([
                    'review_id' => $review->id,
                    'image_url' => $imageUrl,
                    'image_key' => $imageKey,
                ]);
            }
        }

        return response()->json(['message' => '리뷰가 성공적으로 등록되었습니다.', 'review' => $review], 201);
    }


    // 리뷰 삭제
    public function destroy(string $id)
    {
        $review = $this->reviewModel->where('id', $id)->where('user_id', Auth::id())->first();

        // 리뷰가 없거나 현재 사용자가 소유한 리뷰가 아닌 경우
        if (!$review) {
            return response()->json([
                'message' => '리뷰를 찾을 수 없습니다.',
                'errors' => null
            ], 403);
        }

        // 리뷰 삭제
        $review->delete();

        return response()->json(['message' => '리뷰가 정상적으로 삭제되었습니다.'], 200);
    }
}
