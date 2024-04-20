<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

/**
 * 이 작업은 의약품 데이터 API에서 페이지 단위로 데이터를 가져오기 위해 개별 페이지 처리 작업을 큐에 디스패치
 * ShouldQueue 인터페이스를 구현하여 Laravel의 큐 시스템을 사용할 수 있도록 함
 */
class DispatchDrugFetchingJobs implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  // 총 페이지 수
  public $totalPages;

  /**
   * 총 페이지 수를 받아 객체를 초기화
   * @param int $totalPages API에서 가져올 총 페이지 수
   */
  public function __construct($totalPages)
  {
    $this->totalPages = $totalPages;
  }

  /**
   * 각 페이지 번호에 대해 FetchAndStoreDrugs 작업을 디스패치하여,
   * 각 페이지의 데이터를 병렬로 처리하도록 스케줄링
   */
  public function handle()
  {

    Log::info("디스패치 시작: 총 {$this->totalPages} 페이지 처리 예정");

    for ($pageNo = 1; $pageNo <= $this->totalPages; $pageNo++) {
      // 각 페이지 번호에 대해 FetchAndStoreDrugs 작업을 큐에 추가
      // 'drugs' 큐를 지정하여 관련 작업들이 특정 큐에서 처리되도록 함

      // FetchAndStoreDrugs::dispatch()로 하니까 작동 안 됨
      Queue::push(new FetchAndStoreDrugs($pageNo));
      Log::info("페이지 {$pageNo} 큐에 추가됨");
    }
    Log::info("모든 페이지 디스패치 완료");
  }
}
