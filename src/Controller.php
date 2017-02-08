<?php
namespace Korri;

use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Model\Address;
use Jacwright\RestServer\RestException;
use Korri\Requester\Geolocation;
use Korri\Requester\Weather;

class Controller
{

    /**
     * @var Geolocation
     */
    protected $locationRequester;
    /**
     * @var Weather
     */
    protected $weatherRequester;

    /**
     * Controller constructor.
     * @param Geolocation $locationRequester
     * @param Weather $weatherRequester
     */
    public function __construct(Geolocation $locationRequester, Weather $weatherRequester)
    {
        $this->locationRequester = $locationRequester;
        $this->weatherRequester = $weatherRequester;
    }

    /**
     * @param string $ip
     * @param string $service
     * @return Address
     * @throws RestException
     */
    protected function getAddress($ip, $service = 'ip-api')
    {
        $ip = $ip ?: $_SERVER['REMOTE_ADDR'];
        try {
            $result = $this->locationRequester->getLocation($ip, $service);

            return $result->first();

        } catch (UnsupportedOperation $e) {
            throw new RestException(500, 'Invalid IP address');
        } catch (\Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }


    /**
     * Returns the geo location by IP
     * @url GET /geolocation
     * @url GET /geolocation/$ip
     */
    public function getGeolocation($ip = null)
    {
        $service = isset($_GET['service']) ? $_GET['service'] : 'ip-api';

        $address = $this->getAddress($ip, $service);
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
    }


    /**
     * Returns the geo location by IP
     * @url GET /weather
     * @url GET /weather/$ip
     */
    public function getWeather($ip = null)
    {
        //First get the address
        $service = isset($_GET['service']) ? $_GET['service'] : 'ip-api';
        $address = $this->getAddress($ip, $service);
        try {
            $weather = $this->weatherRequester->getWeatherFromCity($address->getLocality(), $address->getCountry()->getCode());

            return [
                'ip' => $ip,
                'city' => $weather->name,

                "temperature" => [
                    "current" => $weather->main->temp,
                    "low" => $weather->main->temp_min,
                    "high" => $weather->main->temp_max,
                ],
                "wind" => [
                    "speed" => $weather->wind->speed,
                    "direction" => $weather->wind->deg
                ]
            ];

        } catch (UnsupportedOperation $e) {
            throw new RestException(500, 'Invalid IP address');
        } catch (\Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}