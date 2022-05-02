<?php

namespace App\Jobs;

use App\Managers\SFCManager;
use App\Managers\SSVManager;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConsultComplaints implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SFCManager $sfcManager, SSVManager $ssvManager)
    {
        $complaintsCodes = [];
        $sfcResponse = $sfcManager->consultComplaints();
        if (!isset($sfcResponse->error)) {
            $complaints = $sfcResponse->results;
            
            if (count($complaints)) {
                $ssvResponse = $ssvManager->massCreationOfComplaints($complaints);
                
                if (!isset($ssvResponse->error) && $ssvResponse->statusCode == 201) {
                    $complaintsCodes = $sfcManager->getComplaintsCode($complaints);
        
                    while (!isset($sfcResponse->error) && !is_null($sfcResponse->next) && $ssvResponse->statusCode == 201) {
                        $sfcResponse = $sfcManager->nextPage($sfcResponse->next);
                        $complaints = $sfcResponse->results;
                        $ssvResponse = $ssvManager->massCreationOfComplaints($complaints);
                        if ($ssvResponse->statusCode == 201) {
                            $complaintsCodes = array_merge($complaintsCodes, $sfcManager->getComplaintsCode($complaints));
                        }
                    }
                    
                    $ssvResponse = $sfcManager->synchronizeComplaints($complaintsCodes);
                    
                    if (count($ssvResponse->pqrs_error)) {
                        Log::info("Error al sincronizar las quejas:\n" . json_encode($ssvResponse->pqrs_error));
                    }
                }
            }
        }
        
        $this->dispatch()->onQueue('get_complaints')->delay(now()->addMinutes(10));
            
    }
}
