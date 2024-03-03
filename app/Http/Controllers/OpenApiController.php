<?php

namespace App\Http\Controllers;

use App\Jobs\FetchAndStoreDrugs;
use Illuminate\Http\Request;

class OpenApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function fetchDrugs()
    {
        FetchAndStoreDrugs::dispatch();

        return response()->json(['message' => 'OPEN API 데이터 가져오기 및 저장이 시작되었습니다.'], 200);
    }

}