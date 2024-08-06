<?php

namespace Tests\Feature;

use Tests\TestCase;

class LookupControllerTest extends TestCase
{
    /**
     * Test the lookup endpoint for Minecraft username.
     *
     * @return void
     */
    public function testLookupMinecraftUsername()
    {
        $response = $this->get('/lookup?type=minecraft&username=Notch');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'username',
            'id',
            'avatar'
        ]);
    }
}
