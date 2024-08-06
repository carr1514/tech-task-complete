<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\OnlySupportsIDsException;

/**
 * Class SteamLookupService
 * @implements LookupServiceInterface
 */
class SteamLookupService implements LookupServiceInterface
{
    /** ** @var Client  */
    protected $client;

    /**
     * Initialize the service
     *
     * @param Client $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Lookup a user by their username
     *
     * @param  $username
     * @return void
     * @throws Exception
     */
    public function lookupByUsername($username)
    {
        throw new OnlySupportsIDsException();
    }

    /**
     * Lookup a user by their ID
     *
     * @param int $id
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function lookupById($id)
    {
        $cacheKey = "steam_user_{$id}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData)
        {
            return $cachedData;
        }

        try
        {
            $response = $this->client->get("https://ident.tebex.io/usernameservices/4/username/{$id}");
            $match    = json_decode($response->getBody()->getContents());

            if (!isset($match->username) || !isset($match->id))
            {
                throw new Exception("No username of ID returned by SteamAPI");
            }

            $data = [
                'username' => $match->username,
                'id'       => $match->id,
                'avatar'   => $match->meta->avatar ?? null // Assuming the avatar potentially could be not returned by the API
            ];

            Cache::put($cacheKey, $data, now()->addMinutes(10));

            return $data;
        }
        catch (GuzzleException $e)
        {
            throw new Exception("Failed to communicate with Steam API: " . $e->getMessage());
        }
    }
}
