<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command;
use App\Classes\Tally;
   
class TallyOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tally:order';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
         $addOrders = new Tally();
         $sales = $addOrders->putXml();

        \Log::info("Tally Order is working fine!");
          
        $this->info('Tally:Order Cummand Run successfully!');
    }
}