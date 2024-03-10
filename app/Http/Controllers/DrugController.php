<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use Illuminate\Http\Request;

class DrugController extends Controller
{

    // 약 정보 조회(약품명, 제조사명, 의약품 내용 키워드 검색 / 10개씩 페이징 처리, 쿼리스트링으로 검색어 받아옴)
    public function index(Request $request)
    {
        // 검색어를 받아옴
        $search = $request->input('search');

        // 검색어가 없을 경우
        if (!$search) {
            return response()->json(['message' => '검색어를 입력해주세요.'], 400);
        }

        // 검색어가 있을 경우
        $drugs = Drug::where('item_name', 'like', '%' . $search . '%')
            ->orWhere('entp_name', 'like', '%' . $search . '%')
            ->orWhere('efcy_qesitm', 'like', '%' . $search . '%')
            ->paginate(10);

        // json 형태로 반환
        return response()->json(['drugs' => $drugs, 'message' => '의약품 정보가 성공적으로 조회되었습니다.'], 200);
    }

    // item_seq를 기준으로 약 정보 조회 
    public function show($id)
    {
        $drug = Drug::where('item_seq', $id)->first();

        // 만약, 해당하는 약 정보가 없을 경우
        if (!$drug) {
            return response()->json(['message' => '해당하는 약 정보가 없습니다.'], 404);
        }

        // json 형태로 반환
        return response()->json(['drug' => $drug, 'message' => '의약품 정보가 성공적으로 조회되었습니다.'], 200);
    }
}
