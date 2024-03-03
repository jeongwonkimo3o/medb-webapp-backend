<?php

namespace App\Jobs;

use App\Models\Drug;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchAndStoreDrugs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $totalPages = 47;
        $url = config('api.url');
        $serviceKey = config('api.service_key');        

        for ($pageNo = 1; $pageNo <= $totalPages; $pageNo++) {
            Log::info("Fetching data for page: {$pageNo}");

            $response = Http::get($url, [
                'serviceKey' => $serviceKey,
                'pageNo' => $pageNo,
                'numOfRows' => 100,
                'type' => 'json'
            ]);
            

            $data = $response->json();
            Log::info('API Response: ', ['response' => $data]);



            foreach ($data['body']['items'] as $item) {
                Drug::create([
                    'entp_name' => $item['entpName'] ?? null,
                    'item_name' => $item['itemName'],
                    'item_seq' => $item['itemSeq'],
                    'efcy_qesitm' => $item['efcyQesitm'] ?? null,
                    'use_method_qesitm' => $item['useMethodQesitm'] ?? null,
                    'atpn_warn_qesitm' => $item['atpnWarnQesitm'] ?? null,
                    'intrc_qesitm' => $item['intrcQesitm'] ?? null,
                    'se_qesitm' => $item['seQesitm'] ?? null,
                    'deposit_method_qesitm' => $item['depositMethodQesitm'] ?? null,
                    'item_image' => $item['itemImage'] ?? null,
                ]);
            }
        }
    }
}
