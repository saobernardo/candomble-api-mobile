<?php

namespace App\Services;

use Carbon\Carbon;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use stdClass;
use UnexpectedValueException;

/**
 * Class JWTService
 *
 * Responsible for generating JWT tokens using a configurable secret, algorithm, and issuer.
 */
class JWTService
{
    private string $privateKey;
    private string $alg;
    private string $issuer;

    /**
     * JWTService constructor.
     *
     * Loads JWT configuration values from the application's config.
     */
    public function __construct()
    {
        $jwtConfig = config('jwt');

        $this->privateKey = $jwtConfig['secret'];
        $this->alg = $jwtConfig['alg'];
        $this->issuer = $jwtConfig['issuer'];
    }

    /**
     * Create JWT Token
     *
     * Creates a signed JWT token with standard claims.
     *
     * @param  array  $prePayload  The custom claims to include in the token (e.g., user ID, roles).
     *
     * @return string The encoded JWT token.
     */
    public function createJWTToken(array $prePayload): string
    {
        $payload = $prePayload;
        $payload['iss'] = $this->issuer;
        $payload['aud'] = $this->issuer;
        $payload['iat'] = Carbon::now()->timestamp;
        $payload['exp'] = Carbon::now()->addYear()->timestamp;

        return JWT::encode($payload, $this->privateKey, $this->alg);
    }

    /**
     * Decode and validate a JWT token.
     *
     * @param  string  $token  The JWT string to be decoded.
     *
     * @return stdClass The decoded JWT payload.
     *
     * @throws ExpiredException When the token has expired.
     * @throws SignatureInvalidException When the token signature is invalid.
     * @throws BeforeValidException When the token is not yet valid (nbf/iat).
     * @throws UnexpectedValueException When the token is malformed or invalid.
     */
    public function decodeJWTToken(string $token): stdClass
    {
        return JWT::decode($token, new Key($this->privateKey, $this->alg));
    }
}
