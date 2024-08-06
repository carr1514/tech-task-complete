<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

/**
 * Class XblLookupService
 * @implements LookupServiceInterface
 */
class XblLookupService implements LookupServiceInterface
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
     * @param $username
     * @return array
     * @throws Exception
     */
    public function lookupByUsername($username)
    {

        $cacheKey = "xbl_user_{$username}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData)
        {
            return $cachedData;
        }

        try
        {
            $response = $this->client->get("https://ident.tebex.io/usernameservices/3/username/{$username}?type=username");
            $profile  = json_decode($response->getBody()->getContents());

            if (!isset($profile->username) || !isset($profile->id))
            {
                throw new Exception("No username of ID returned by XBL API");
            }

            $data = [
                'username' => $profile->username,
                'id'       => $profile->id,
                'avatar'   => $profile->meta->avatar ?? null // Assuming the avatar potentially could be not returned by the API
            ];

            Cache::put($cacheKey, $data, now()->addMinutes(10));

            return $data;
        }
        catch (GuzzleException $e)
        {
            throw new Exception("Failed to communicate with XBL API: " . $e->getMessage());
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
        $cacheKey = "xbl_user_{$id}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData)
        {
            return $cachedData;
        }

        try
        {
            $response = $this->client->get("https://ident.tebex.io/usernameservices/3/username/{$id}");
            $profile  = json_decode($response->getBody()->getContents());

            if (!isset($profile->username) || !isset($profile->id))
            {
                throw new Exception("No username of ID returned by xblAPI");
            }

            $data = [
                'username' => $profile->username,
                'id'       => $profile->id,
                'avatar'   => $profile->meta->avatar ?? null // Assuming the avatar potentially could be not returned by the API
            ];

            Cache::put($cacheKey, $data, now()->addMinutes(10));

            return $data;
        }
        catch (GuzzleException $e)
        {
            throw new Exception("Failed to communicate with XBL API: " . $e->getMessage());
        }
    }
}
