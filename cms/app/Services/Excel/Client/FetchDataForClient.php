<?php


namespace App\Services\Excel\Client;

use App\PartyType;

/**
 * class FetchDataForClient
 *
 * @package: App\Services\Excel\Client
 * @author: Shahsank Jha <shashank.deltatech@gmail.com>
 */

class FetchDataForClient
{
    public $companyId;

    public function __construct($companyId) {
        $this->companyId = $companyId;
    }

    /**
     * @param $clientType
     * @return int
     */
    public function getClientTypeId($clientType) {
        // get id and name of the party types and convert the names into lowercase
        $partyTypes = PartyType::companyId($this->companyId)->pluck('name', 'id')->toArray();
        $partyTypes = array_map('strtolower', $partyTypes);

        // check for the existence of party type and return key
        if (in_array($clientType, $partyTypes)) return (int) array_search($clientType, $partyTypes);
        return 0;
    }
}