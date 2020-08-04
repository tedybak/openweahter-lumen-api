<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use BrightNucleus\CountryCodes\Country as Bridge;
use Illuminate\Http\Request;


class OpenWeatherController extends BaseController
{
    public function getByName ( Request $request, $name )    {

        //For temperature in Fahrenheit use units=imperial
        //For temperature in Celsius use units=metric
        //Temperature in Kelvin is used by default, no need to use units parameter in API call

        if ($request->isJson()) {

            $client = new Client();

            $appid = env('OPENWEATHER_KEY');
            $response = $client->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
                'query' => [
                    'q' => $name,
                    'appid' => $appid
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);
            $contents = (string)$response->getBody();
            $content = \GuzzleHttp\json_decode($contents);

            $content_json = json_decode($contents, true);
            $content_json["main"]["fahrenheit"] = array();
            $content_json["main"]["fahrenheit"] = Country::convert($content_json["main"], "fahrenheit");

            $json["main"]["celcius"] = array();
            $content_json["main"]["celcius"] = Country::convert($content_json["main"], "celcius");

            return $content_json;
        }else{
            return response()->json(['error' => 'Unauthorized'],401);
        }
    }

    public function  getByCountryCode ( Request $request,$code )
    {
        if ($request->isJson()) {

            $city = Country::getCountryCapital($code);
            $client = new Client();
            $appid = env('OPENWEATHER_KEY');

            $response = $client->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
                'query' => [
                    'q' => $city,
                    'appid' => $appid
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);

            $contents = (string)$response->getBody();
            $content = \GuzzleHttp\json_decode($contents);

            $content_json = json_decode($contents, true);
            $content_json["main"]["fahrenheit"] = array();
            $content_json["main"]["fahrenheit"] = Country::convert($content_json["main"], "fahrenheit");

            $json["main"]["celcius"] = array();
            $content_json["main"]["celcius"] = Country::convert($content_json["main"], "celcius");

            return $content_json;


        } else {
            return response()->json(['error' => 'Unauthorized'],401);
        }
    }
}
