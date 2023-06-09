<?php

namespace Jexactyl\Http\Requests\Api\Client\Store;

use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;

class PurchaseResourceRequest extends ClientApiRequest
{
    /**
     * Rules to validate this request against.
     */
    public function rules(): array
    {
        return [
            'resource' => 'required|string|in:cpu,memory,disk,slots,ports,backups,databases',
        ];
    }
}
