<?php

namespace Jexactyl\Models\Traits;

use Illuminate\Support\Str;
use Jexactyl\Models\ApiKey;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Jexactyl\Extensions\Laravel\Sanctum\NewAccessToken;

/**
 * @mixin \Jexactyl\Models\Model
 */
trait HasAccessTokens
{
    use HasApiTokens {
        tokens as private _tokens;
        createToken as private _createToken;
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Sanctum::$personalAccessTokenModel);
    }

    public function createToken(?string $memo, ?array $ips): NewAccessToken
    {
        /** @var \Jexactyl\Models\ApiKey $token */
        $token = $this->tokens()->forceCreate([
            'user_id' => $this->id,
            'key_type' => ApiKey::TYPE_ACCOUNT,
            'identifier' => ApiKey::generateTokenIdentifier(ApiKey::TYPE_ACCOUNT),
            'token' => encrypt($plain = Str::random(ApiKey::KEY_LENGTH)),
            'memo' => $memo ?? '',
            'allowed_ips' => $ips ?? [],
        ]);

        return new NewAccessToken($token, $plain);
    }
}
