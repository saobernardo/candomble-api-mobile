<?php

return [
    'secret' => env('JWT_SECRET'),
    'public' => env('JWT_PUBLIC'),
    'ttl' => 60,
    'refresh_ttl' => 20160,
    'alg' => env('JWT_ALG', 'HS256'),
    'issuer' => env('JWT_ISSUER', 'cabelinnaregua.com.br'),
];
