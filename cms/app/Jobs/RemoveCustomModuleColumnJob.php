<?php

namespace App\Jobs;

use App\CustomModule;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Schema;

class RemoveCustomModuleColumnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $field;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $tablename = CustomModule::find($this->field->custom_module_id)->table_name;
        if (Schema::hasColumn($tablename, $this->field->slug)) {
            Schema::table($tablename, function (Blueprint $table) {
                $table->dropColumn($this->field->slug);
            });
        }


    }
}
