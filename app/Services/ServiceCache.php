<?php
namespace Slim\Services;

/**
 * Class ServiceCache
 * @package Slim\Services
 */
abstract class ServiceCache
{
    /**
     * @return mixed
     */
    abstract public function getResult();

    /**
     * @param $psid
     * @param $container
     * @return bool
     */
    public function iscached($psid, $container)
    {
        $data = $container->get('FileCache')->get($psid);
        if( !empty($data))
        {
            return $data->getContent();
        }else{
            return false;
        }
    }

    /**
     * @param $cacheKey
     * @param $result
     * @param $container
     */
    public function cache($cacheKey, $result, $container)
    {
        $ttl = $container->get('config')->get('cache.TestService.ttl');
        $data="cache";
        $container->get('FileCache')->add($cacheKey, $data,200,['test' => 'test'],$ttl);

    }
}