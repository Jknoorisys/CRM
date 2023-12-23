<?php

namespace App\Libraries;

use Tymon\JWTAuth\Facades\JWTAuth;

class Services
{
    public function getSignedAccessTokenForUser($isUservalid, array $claims)
    {
        return JWTAuth::customClaims($claims)->fromUser($isUservalid);
    }
}
