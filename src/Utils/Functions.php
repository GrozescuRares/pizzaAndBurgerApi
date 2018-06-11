<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/22/2018
 * Time: 2:08 PM
 */
class Functions
{
    public function __construct()
    {
    }

    public function createResponse($statusCode, $content){

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode($statusCode);
        $response->setContent(json_encode($content));
        return $response;
    }

    public function sendEmail($post){
        $ch = curl_init('http://travian-npc.000webhostapp.com/sendEmail.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);
        curl_close($ch);


        if($response == "OK")
        {
            return true;
        }
        else{
            return false;
        }


    }

}