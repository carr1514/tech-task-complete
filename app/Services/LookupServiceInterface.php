<?php

namespace App\Services;

/**
 * Interface LookupServiceInterface
 *
 * @package App\Services
 *
 * @property string|int $username
 * @property int $id
 */
interface LookupServiceInterface
{

    /**
     * Lookup a user by their username
     *
     * @param  string|int $username
     * @return array
     */
    public function lookupByUsername($username);

    /**
     * Lookup a user by their ID
     *
     * @param  int $id
     * @return array
     */
    public function lookupById($id);
}
