<?php
namespace Korri\Requester;


use Geocoder\Provider\FreeGeoIp;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Korri\Geocoder\Provider\IpApi;

class Geolocation
{
    const PROVIDERS = [
        'ip-api' => IpApi::class,
        'freegeoip' => FreeGeoIp::class
    ];

    /**
     * @var HttpAdapterInterface
     */
    protected $adapter;

    /**
     * @param HttpAdapterInterface $adapter
     */
    public function __construct(HttpAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get location from IP
     * @param string $ip
     * @param string $service
     *
     * @return AddressCollection
     */
    public function getLocation($ip, $service = 'ip-api')
    {
        if (!in_array($service, self::PROVIDERS)) {
            $service = 'ip-api';
        }
        $class = self::PROVIDERS[$service];
        $providerInstance = new $class($this->adapter);

        return $providerInstance->geocode($ip);
    }
}