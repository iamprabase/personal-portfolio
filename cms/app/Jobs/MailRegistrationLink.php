<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SubscriptionCreated;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MailRegistrationLink implements ShouldQueue
{
    use Dispatchable, SerializesModels;
    private $email;
    private $link;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $link)
    {
      $this->email = $email;
      $this->link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      try{
        Mail::to($this->email)->send(new SubscriptionCreated($this->link));
      }catch(\Exception $e){
        Log::error(array("Mailing Registration Link", $e->getMessage()));
      }
    }
}