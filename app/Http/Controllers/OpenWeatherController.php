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
    public function getByCityCode ( Request $request, $name )    {

        // Temperature in Kelvin is used by default, no need to use units parameter in API call
        // For temperature in Fahrenheit use units=imperial
        // For temperature in Celsius use units=metric

        if ($request->isJson()) {

            $client = new Client();

            $appid = env('OPENWEATHER_KEY');

            try {
                $city = Country::getCityByCode(strtoupper($name));
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
                $contents = (string) $response->getBody();
                $content = \GuzzleHttp\json_decode($contents);

                $content_json = json_decode($contents, true);
                $content_json["fahrenheit"] = array();
                $content_json["fahrenheit"] = Country::convert($content_json["main"], "fahrenheit");

                $json["celcius"] = array();
                $content_json["celcius"] = Country::convert($content_json["main"], "celcius");

                return response()->json($content_json,200);
            } catch (\Exception $e){
                return response()->json(['error' => 'Not Found'],404);
            }
        } else{
            return response()->json(['error' => 'Unauthorized'],401);
        }
    }

    public function  getByCountryCode ( Request $request,$code )
    {
        if ($request->isJson()) {

            try {

                $city = Country::getCountryByCode(strtoupper($code));
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
                $content_json["fahrenheit"] = array();
                $content_json["fahrenheit"] = Country::convert($content_json["main"], "fahrenheit");

                $json["celcius"] = array();
                $content_json["celcius"] = Country::convert($content_json["main"], "celcius");

                return response()->json($content_json,200);

            }catch (\Exception $e){
                return response()->json(['error' => 'country code must be in Alpha 2 format'],404);
            }

        } else {
            return response()->json(['error' => 'Unauthorized'],401);
        }
    }
}
