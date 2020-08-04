<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use BrightNucleus\CountryCodes\Country as Bridge;

class OpenWeatherController extends BaseController
{
    public function getByName ($name){

        $client = new Client(['base_uri' => 'https://api.openweathermap.org/data/2.5/weather?q=']);

        //For temperature in Fahrenheit use units=imperial
        //For temperature in Celsius use units=metric
        //Temperature in Kelvin is used by default, no need to use units parameter in API call

        $appid =    env('OPENWEATHER_KEY');

       $response =  $client->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
           'query' => [
               'q' => $name,
                'appid' => $appid
           ],
           'headers'  => [
               'Accept' => 'application/json',
               'Content-Type' => 'application/json',
           ]
        ]);
        $contents = (string) $response->getBody();
        $content = \GuzzleHttp\json_decode($contents);

        $content_json = json_decode($contents, true);
        $content_json["main"]["fahrenheit"] = array();
        $content_json["main"]["fahrenheit"] = Country::convert($content_json["main"],"fahrenheit");

        $json["main"]["celcius"] = array();
        $content_json["main"]["celcius"] = Country::convert($content_json["main"],"celcius");

        return $content_json;
    }

    public function  getByCountryCode ($code){

        $city =  Country::getCountryCapital($code);
        $client = new Client();
        $appid =    env('OPENWEATHER_KEY');

        $response =  $client->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
            'query' => [
                'q' => $city,
                'appid' => $appid
            ],
            'headers'  => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);

        $contents = (string) $response->getBody();
        $content = \GuzzleHttp\json_decode($contents);

        $content_json = json_decode($contents, true);
        $content_json["main"]["fahrenheit"] = array();
        $content_json["main"]["fahrenheit"] = Country::convert($content_json["main"],"fahrenheit");

        $json["main"]["celcius"] = array();
        $content_json["main"]["celcius"] = Country::convert($content_json["main"],"celcius");

        return $content_json;


     }
}
