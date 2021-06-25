<?php

namespace App\Listeners;

use App\Events\CustomPlanUdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateUpdateSubscriptionInModuleListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CustomPlanUdated  $event
     * @return void
     */
    public function handle(CustomPlanUdated $event)
    {
      SwitchSubscriptionModules::dispatch($planId);
    }
}
