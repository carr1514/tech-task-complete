<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

/**
 * Class MinecraftLookupService
 * @implements LookupServiceInterface
 */
class MinecraftLookupService implements LookupServiceInterface
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
     * @param  string $username
     * @return array
     * @throws Exception
     */
    public function lookupByUsername($username)
    {
        $cacheKey   = "minecraft_user_{$username}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData)
        {
            return $cachedData;
        }

        try
        {
            $response = $this->client->get("https://api.mojang.com/users/profiles/minecraft/{$username}");
            $match    = json_decode($response->getBody()->getContents());

            $data = [
                'username' => $match->name,
                'id'       => $match->id,
                'avatar'   => "https://crafatar.com/avatars/{$match->id}"
            ];

            Cache::put($cacheKey, $data, now()->addMinutes(10));

            return $data;
        }
        catch (GuzzleException $e)
        {
            throw new Exception("Failed to communicate with Steam API: " . $e->getMessage());
        }
    }

    /**
     * Lookup a user by their ID
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    public function lookupById($id)
    {

        $cacheKey   = "minecraft_user_{$id}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData)
        {
            return $cachedData;
        }

        try
        {
            $response = $this->client->get("https://sessionserver.mojang.com/session/minecraft/profile/{$id}");
            $match    = json_decode($response->getBody()->getContents());

            $data = [
                'username' => $match->name,
                'id'       => $match->id,
                'avatar'   => "https://crafatar.com/avatars/{$match->id}"
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
