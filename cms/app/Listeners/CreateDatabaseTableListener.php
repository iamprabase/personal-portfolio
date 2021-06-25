<?php

namespace App\Listeners;

use App\Events\CustomModuleCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateDatabaseTableListener
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
     * @param  CustomModuleCreated  $event
     * @return void
     */
    public function handle(CustomModuleCreated $event)
    {
        //
    }
}
