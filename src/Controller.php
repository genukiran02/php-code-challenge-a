<?php
namespace Korri;

use Geocoder\Exception\UnsupportedOperation;
use Jacwright\RestServer\RestException;
use Korri\Requester\Geolocation;

class Controller
{

    /**
     * @var Geolocation
     */
    protected $locationRequester;

    /**
     * Controller constructor.
     * @param Geolocation $locationRequester
     */
    public function __construct(Geolocation $locationRequester)
    {
        $this->locationRequester = $locationRequester;
    }


    /**
     * Returns the geo location by IP
     * @url GET /geolocation
     * @url GET /geolocation/$ip
     */
    public function getGeolocation($ip = null)
    {
        $ip = $ip ?: $_SERVER['REMOTE_ADDR'];
        $service = isset($_GET['service']) ? $_GET['service'] : 'ip-api';
        try {
            $result = $this->locationRequester->getLocation($ip, $service);

            $address = $result->first();
            $adminLevels = $address->getAdminLevels();

            return [
                'ip' => $ip,
                'geo' => [
                    'service' => $service,
                    'city' => $address->getLocality(),
                    'region' => $adminLevels->count() > 0 ? $adminLevels->first()->getName() : '',
                    'country' => $address->getCountry()->getName()
                ]
            ];
        }catch (UnsupportedOperation $e) {
            throw new RestException(500, 'Invalid IP address');
        } catch (\Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}