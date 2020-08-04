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

        $json = json_decode($contents, true);

        $json["main"]["fahrenheit"] = array();
        $json["main"]["fahrenheit"]["temp"] = (float) number_format(( $json["main"]["temp"] - 273.15) * 9 / 5 + 32,2 );
        $json["main"]["fahrenheit"]["feels_like"] = (float) number_format(( $json["main"]["feels_like"] - 273.15) * 9 / 5 + 32,2 );
        $json["main"]["fahrenheit"]["temp_min"] = (float) number_format(( $json["main"]["temp_min"] - 273.15) * 9 / 5 + 32,2 );
        $json["main"]["fahrenheit"]["temp_max"] = (float) number_format(( $json["main"]["temp_max"] - 273.15) * 9 / 5 + 32,2 );

        $json["main"]["celcius"] = array();
        $json["main"]["celcius"]["temp"] = (float) number_format(( $json["main"]["temp"] - 273.15)  ,2 );
        $json["main"]["celcius"]["feels_like"] = (float) number_format(( $json["main"]["feels_like"] - 273.15)  ,2 );
        $json["main"]["celcius"]["temp_min"] = (float) number_format(( $json["main"]["temp_min"] - 273.15)  ,2 );
        $json["main"]["celcius"]["temp_max"] = (float) number_format(( $json["main"]["temp_max"] - 273.15)  ,2 );

        return $json;
    }

    public function  getByCountryCode ($code){

        $city =  Country::getCountryCapital($code);

        $client = new Client(['base_uri' => 'https://api.openweathermap.org/data/2.5/weather?q=']);
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

        $json = json_decode($contents, true);

        $json["main"]["fahrenheit"] = array();
        $json["main"]["fahrenheit"]["temp"] = (float) number_format(( $json["main"]["temp"] - 273.15) * 9 / 5 + 32,2 );
        $json["main"]["fahrenheit"]["feels_like"] = (float)  number_format(( $json["main"]["feels_like"] - 273.15) * 9 / 5 + 32,2 );
        $json["main"]["fahrenheit"]["temp_min"] = (float) number_format(( $json["main"]["temp_min"] - 273.15) * 9 / 5 + 32,2 );
        $json["main"]["fahrenheit"]["temp_max"] = (float) number_format(( $json["main"]["temp_max"] - 273.15) * 9 / 5 + 32,2 );

        $json["main"]["celcius"] = array();
        $json["main"]["celcius"]["temp"] = (float) number_format(( $json["main"]["temp"] - 273.15)  ,2 );
        $json["main"]["celcius"]["feels_like"] = (float) number_format(( $json["main"]["feels_like"] - 273.15)  ,2 );
        $json["main"]["celcius"]["temp_min"] = (float) number_format(( $json["main"]["temp_min"] - 273.15)  ,2 );
        $json["main"]["celcius"]["temp_max"] = (float) number_format(( $json["main"]["temp_max"] - 273.15)  ,2 );

        return $json;
     }
}
