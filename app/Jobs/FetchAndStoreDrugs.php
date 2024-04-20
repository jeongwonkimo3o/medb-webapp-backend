<?php

namespace App\Jobs;

use App\Models\Drug;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchAndStoreDrugs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pageNo;

    public function __construct($pageNo)
    {
        $this->pageNo = $pageNo;
    }

    public function handle()
    {

        $url = config('api.url');
        $serviceKey = config('api.service_key');

        try {
            $response = Http::get($url, [
                'serviceKey' => $serviceKey,
                'pageNo' => $this->pageNo,
                'numOfRows' => 100,
                'type' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("API 응답 성공: 페이지 {$this->pageNo}", ['items_count' => count($data['body']['items'])]);

                foreach ($data['body']['items'] as $item) {
                    // item_seq를 기준으로 데이터를 저장(DB에 없는 값)하거나 
                    // 업데이트(기존값은 업데이트 됐을 경우에만 업데이트, 업데이트 안됐을 경우 기존값 유지)
                    $drug =  Drug::updateOrCreate(
                        ['item_seq' => $item['itemSeq']],
                        [
                            'entp_name' => $item['entpName'] ?? null,
                            'item_name' => $item['itemName'],
                            'efcy_qesitm' => $item['efcyQesitm'] ?? null,
                            'use_method_qesitm' => $item['useMethodQesitm'] ?? null,
                            'atpn_warn_qesitm' => $item['atpnWarnQesitm'] ?? null,
                            'intrc_qesitm' => $item['intrcQesitm'] ?? null,
                            'se_qesitm' => $item['seQesitm'] ?? null,
                            'deposit_method_qesitm' => $item['depositMethodQesitm'] ?? null,
                            'item_image' => $item['itemImage'] ?? null
                        ]
                    );

                    $operation = $drug->wasRecentlyCreated ? '생성됨' : '업데이트됨';
                    Log::info("데이터베이스 작업: {$operation}", ['item_seq' => $item['itemSeq']]);
                }
                // 마지막 업데이트 시간을 캐시에 저장
                Cache::put('last_updated', now()->toDateTimeString());
            } else {
                Log::error('API 응답 실패: 페이지 {$this->pageNo}', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('API 호출 중 예외 발생', ['message' => $e->getMessage(), 'page' => $this->pageNo]);
        }
    }
}
