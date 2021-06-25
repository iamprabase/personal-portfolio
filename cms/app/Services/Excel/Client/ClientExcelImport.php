<?php


namespace App\Services\Excel\Client;


use App\Client;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;

/**
 * class ClientExcelImport
 *
 * @package: App\Services\Excel\Client
 * @author: Shahsank Jha <shashank.deltatech@gmail.com>
 */

class ClientExcelImport implements ToArray
{
    public function array(array $row)
    {
        // extending the max execution time
        ini_set('max_execution_time', '0');

        // extending the memory limit
        ini_set('memory_limit', '-1');

        // get company Id
        $companyId = config('settings.company_id');

        // instantiate the FetchDataForClient class
        $fetchDataForClients = new FetchDataForClient($companyId);

        foreach ($row as $key => $value) if ($key != 0) {
            $name = $value[0];
            $companyName = $value[1];
            $clientTypeId = $fetchDataForClients->getClientTypeId(strtolower($value[2]));

            try {
                // begin DB transaction
                DB::beginTransaction();

                $client = new Client;
                $client->company_id = $companyId;
                $client->name = $name;
                $client->company_name = $companyName;
                $client->client_type = $clientTypeId;
                $client->save();

                // commit transaction
                DB::commit();
            } catch (\Exception $error) {
                DB::rollBack();
                dd($error->getMessage());
            }
        }
    }
}