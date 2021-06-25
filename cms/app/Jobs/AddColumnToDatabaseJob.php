<?php

namespace App\Jobs;

use App\CustomModule;
use App\CustomModuleField;
use App\FormField;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Schema;

class AddColumnToDatabaseJob
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $customModuleForm;

    /**
     * Create a new job instance.
     *
     * @param CustomModuleField $customModuleForm
     */
    public function __construct(CustomModuleField $customModuleForm)
    {
        $this->customModuleForm = $customModuleForm;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $customModuleForm = $this->customModuleForm;
        $customModule = CustomModule::find($this->customModuleForm->custom_module_id);

        Schema::table($customModule->table_name, function (Blueprint $table) use ($customModuleForm) {
            $table->{config('migrationhelper.type.' . $customModuleForm->type)}($customModuleForm->slug)->nullable()->after('company_id');
        });
    }
}
