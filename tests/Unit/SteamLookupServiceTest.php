<?php

namespace Tests\Unit;

use App\Exceptions\OnlySupportsIDsException;
use App\Services\SteamLookupService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\TestCase;

/**
 * Steam Lookup testing
 *
 */
class SteamLookupServiceTest extends TestCase
{
    /**
     * Test lookup the steamAPI by a user
     *
     * @throws GuzzleException
     */
    public function testLookupByIdFetchesAndCachesData()
    {
        // The SteamID of our test user
        $steamID  = '76561198806141009';
        $username = 'tebex';

        // Let's make sure the cache does not have the data initially
        Cache::shouldReceive('get')
            ->once()
            ->with('steam_user_' . $steamID)
            ->andReturn(null);

        // Now we mock the HTTP client response
        $response = new Response(200, [], json_encode([
            'username' => $username,
            'id'       => $steamID
        ]));

        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn($response);

        // We check cache to see if it's stored the data
        Cache::shouldReceive('put')
            ->once()
            ->with('steam_user_' . $steamID, [
                'username' => $username,
                'id' => $steamID,
                'avatar' => null
            ], \Mockery::any());

        $service = new SteamLookupService($client);

        $result = $service->lookupById($steamID);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('id', $result);
    }

    /**
     * Test lookup the steamAPI by a username ( should throw an error )
     *
     * @throws Exception
     */
    public function testLookupByUsernameReturnsError()
    {
        // The username of our test user we want to lookup
        $username = 'test';

        $response = new Response(400, [], json_encode([
            'error' => 'Steam only supports IDs'
        ]));

        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn($response);

        $service = new SteamLookupService($client);

        $this->expectException(OnlySupportsIDsException::class);

        $service->lookupByUsername($username);
    }

}
