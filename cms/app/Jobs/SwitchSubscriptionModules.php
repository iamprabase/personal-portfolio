<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SwitchSubscriptionModules implements ShouldQueue
{
    use Dispatchable, SerializesModels;
    private $planId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($planId)
    {
      $this->planId = $planId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      
    }
}