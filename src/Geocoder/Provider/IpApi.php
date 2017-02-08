<?php
/**
 * @license    MIT License
 */
namespace Korri\Geocoder\Provider;

use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\AbstractHttpProvider;
use Geocoder\Provider\Provider;

/**
 * Base on the FreeGeoIp provider
 * @author Hugo Vacher
 */
final class IpApi extends AbstractHttpProvider  implements Provider
{
    /**
     * @var string
     */
    const ENDPOINT_URL = 'http://ip-api.com/json/%s';

    /**
     * {@inheritDoc}
     */
    public function geocode($address)
    {
        if (!filter_var($address, FILTER_VALIDATE_IP)) {
            throw new UnsupportedOperation('The IpApi provider does not support street addresses.');
        }
        if (in_array($address, array('127.0.0.1', '::1'))) {
            return $this->returnResults([$this->getLocalhostDefaults()]);
        }
        $query = sprintf(self::ENDPOINT_URL, $address);
        return $this->executeQuery($query);
    }

    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude)
    {
        throw new UnsupportedOperation('The IpApi provider is not able to do reverse geocoding.');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ip_api';
    }

    /**
     * @param string $query
     *
     * @return AddressCollection
     */
    private function executeQuery($query)
    {
        $content = (string) $this->getAdapter()->get($query)->getBody();

        if (empty($content)) {
            throw new NoResult(sprintf('Could not execute query %s', $query));
        }
        $data = (array)json_decode($content);
        if (empty($data)) {
            throw new NoResult(sprintf('Could not execute query %s', $query));
        }
        $adminLevels = [];
        if (!empty($data['regionName']) || !empty($data['region'])) {
            $adminLevels[] = [
                'name' => isset($data['regionName']) ? $data['regionName'] : null,
                'code' => isset($data['region']) ? $data['region'] : null,
                'level' => 1
            ];
        }
        return $this->returnResults([
            array_merge($this->getDefaults(), array(
                'latitude' => isset($data['lat']) ? $data['lat'] : null,
                'longitude' => isset($data['lon']) ? $data['lon'] : null,
                'locality' => isset($data['city']) ? $data['city'] : null,
                'postalCode' => isset($data['zip']) ? $data['zip'] : null,
                'adminLevels' => $adminLevels,
                'country' => isset($data['country']) ? $data['country'] : null,
                'countryCode' => isset($data['countryCode']) ? $data['countryCode'] : null,
                'timezone' => isset($data['timezone']) ? $data['timezone'] : null,
            ))
        ]);
    }
}