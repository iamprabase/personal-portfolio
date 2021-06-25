<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command;
use App\Classes\Tally;
   
class TallyParty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tally:party';
    
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
         $addParty = new Tally();
         $sales = $addParty->getPartyXml();

        \Log::info("Tally Party Added Successfully!");
          
        $this->info('Tally:Party Cummand Run successfully!');
    }
}