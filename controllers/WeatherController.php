<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\JsonParser ;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class WeatherController extends Controller
{
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
            ],
        ];
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionCity($id = null)
    {
       
        $apikey = '9cb14f9d38833f4d901edd403fbfd9f5';
        $cityName = $id != null ? $id :'' ;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://api.openweathermap.org/data/2.5/weather?q=".$cityName."&APPID=".$apikey);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        $dataWeather =  json_decode($result);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if($dataWeather->cod != 200){
            return array('status' => false, 'data'=> $dataWeather);
        }

        $kelvin = $dataWeather->main->temp;
        $dataWeather->main->temp_celcius = round($kelvin - 273.15);

        $weather = new \stdClass();
        $weather->temp = isset($dataWeather->main) ? $dataWeather->main->temp_celcius : null;
        $weather->weather_type = isset($dataWeather->weather) ? $dataWeather->weather[0]->main : null;
        $weather->weather_icon = isset($dataWeather->weather) ? 'http://openweathermap.org/img/wn/'.$dataWeather->weather[0]->icon.'@2x.png'  : null;
        $weather->name = isset($dataWeather->name) ? $dataWeather->name : null;
        $weather->country = isset($dataWeather->sys) ? $dataWeather->sys->country : null;
        $weather->coord = isset($dataWeather->coord) ? $dataWeather->coord : null;

       
        return array('status' => true, 'data'=> $dataWeather);
    }
}
