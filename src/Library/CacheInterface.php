<?php
/**
 * CacheInterface.php - API
 *
 * @author    Marc Towler <marc.towler@designdeveloprealize.com>
 *
 * @copyright 2018 - Design Develop Realize
 */

namespace API\Library;


interface CacheInterface
{
    /**
     * @param string $key Checks whether or not the cache contains unexpired data for the specified key

     * @return bool
     */
    public function has($key);

    /**
     * @param string $key Gets data for specified key

     * @return string|null Returns null if the cached item doesn't exist or has expired
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl Time in seconds before the data becomes expired
     * @return mixed
     */
    public function put($key, $data, $ttl = 0);
}