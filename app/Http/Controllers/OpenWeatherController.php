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
    public function getCityByCode ( Request $request, $cityCode )    {

        // Temperature in Kelvin is used by default, no need to use units parameter in API call
        // For temperature in Fahrenheit use units=imperial
        // For temperature in Celsius use units=metric

        if ($request->isJson()) {

            $client = new Client();

            $appid = env('OPENWEATHER_KEY');
            $default = env('FORMAT_SISTEM');
            $baser_url = env('BASE_URL');


            try {
                $city = Country::getCityByCode(strtoupper($cityCode));


                 $response = $client->request('GET', $baser_url, [
                    'query' => [
                        'q' => $city,
                        'appid' => $appid,
                        'units' => $default
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

                $json["kelvin"] = array();
                $content_json["kelvin"] = Country::convert($content_json["main"], "kelvin");

                return response()->json($content_json,200);
            } catch (\Exception $e){
                return response()->json(['error' => 'Not Found'],404);
            }
        } else{
            return response()->json(['error' => 'Unauthorized, not available from browser'],401);
        }
    }

    public function  getCountryByCode ( Request $request,$code )
    {
        if ($request->isJson()) {

            try {
                $city = Country::getCountryByCode(strtoupper($code));
                $client = new Client();
                $appid = env('OPENWEATHER_KEY');
                $default = env('FORMAT_SISTEM');
                $baser_url = env('BASE_URL');

                $response = $client->request('GET', $baser_url, [
                    'query' => [
                        'q' => $city,
                        'appid' => $appid,
                        'units' => $default
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

                $json["kelvin"] = array();
                $content_json["kelvin"] = Country::convert($content_json["main"], "kelvin");

                return response()->json($content_json,200);

            }catch (\Exception $e){
                return response()->json(['error' => 'country code must be in Alpha 2 format'],404);
            }

        } else {
            return response()->json(['error' => 'Unauthorized, not available from browser'],401);
        }
    }
}
