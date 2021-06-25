<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Schema;

class DropCustomModuleTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tablename;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tablename)
    {
        $this->tablename = $tablename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Schema::dropIfExists($this->tablename);
    }
}
