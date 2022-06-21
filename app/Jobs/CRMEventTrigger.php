<?php

namespace App\Jobs;

use App\Http\Clients\SSVClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CRMEventTrigger implements ShouldQueue
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
    public function handle(SSVClient $ssvClient)
    {
        $ssvClient->sendData('GET', 'sfc_modificar', []);

        $this->dispatch()->onQueue('crm_event_trigger')->delay(now()->addMinutes(1));

        $now = now('America/Bogota');
        $hour = $now->hour;

        if (($hour >= 18 && $hour <= 23) || ($hour >= 0 && $hour < 5)) {
            $this->dispatch()->onQueue('crm_event_trigger')->delay(now()->addMinutes(1));
        } else {
            $nextExecutionTime = Carbon::createFromTimeString('18:00:00', 'America/Bogota');
            $this->dispatch()->onQueue('crm_event_trigger')->delay($nextExecutionTime);
            
        }
    }
}
