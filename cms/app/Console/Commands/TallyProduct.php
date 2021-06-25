<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command;
use App\Classes\Tally;
   
class TallyProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tally:product';
    
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
         $addProduct = new Tally();
         $sales = $addProduct->getProductXml();

        \Log::info("Tally Product Added Successfully!");
          
        $this->info('Tally:Product Cummand Run successfully!');
    }
}