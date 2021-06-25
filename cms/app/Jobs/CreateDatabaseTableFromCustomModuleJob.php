<?php

namespace App\Jobs;

use App\CustomModule;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseTableFromCustomModuleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customModule;
    public $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CustomModule $customModule)
    {
        $this->customModule = $customModule;
        $this->company_id = config('settings.company_id');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Schema::create($this->customModule->table_name, function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
        });
    }
}
