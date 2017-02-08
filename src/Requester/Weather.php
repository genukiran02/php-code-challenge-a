<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 17-02-08
 * Time: 17:55
 */

namespace Korri\Requester;


use Ivory\HttpAdapter\HttpAdapterInterface;

class Weather
{

    const API_URL = 'http://api.openweathermap.org/data/2.5/weather?units=metric&q=%s&appid=6103b0f582e78c7382bc6b0cdc06deb8';
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
     * @param string $city Name of the city
     * @param string $country Country code
     * @return object Json Response
     */
    public function getWeatherFromCity($city, $country)
    {
        $response = $this->adapter->get(sprintf(self::API_URL, urlencode($city . ',' . $country)));
        return (object)json_decode($response->getBody());
    }
}