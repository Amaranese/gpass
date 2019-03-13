<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\PayUService\Exception;
use \Firebase\JWT\JWT;
use App\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected $key = 'JOAQUIN';

    protected function error($code, $message)
    {

        $json = ['message' => $message];
        $json = json_encode($json);
        return  response($json, $code)->header('Access-Control-Allow-Origin', '*');
    }

    protected function success($message, $data = [])
    {
    	$json = ['message' => $message, 'data' => $data];
        $json = json_encode($json);
        return  response($json, 200)->header('Access-Control-Allow-Origin', '*');
    }

    protected function getOneHeader($header)
    {
    	$headers = getallheaders();

    	if(isset($headers[$header]))
    	{
    		$header = $headers[$header];
    		return $header;
    	}
    	return null;	
    }

    private function getToken()
    {
    	$token = $this->getOneHeader("Authorization");

    	if(is_null($token))
    	{
    		return $this->error(400, "Primero logeate");
    	}
    	return $token;
    }

    protected function getUserData()
    {
        try 
        {
            $userData = JWT::decode($this->getToken(), $this->key, array('HS256'));
            return $userData;      
        } 
        catch (\Exception $e) 
        {
            return null;
        }	
    }

	protected function checkLogin()
    {
    	$userData = $this->getUserData();

        if(is_null($userData))
        {
            return false;
        }

        $userSave = User::where('email', $userData->email)->first();

        $passwordSave = $this->decodificar($userSave->password);
        $passwordData = $this->decodificar($userData->password);

        if(!is_null($userSave) && $passwordSave == $passwordData)
        {
            return true;
        }
        return false;
    }  

    protected function deleteAllSpace($string)
    {
       $string = str_replace(' ', '', $string);
       return $string;
    } 

    protected function codificar($dato) {
        $resultado = $dato;
        $arrayLetras = array('J', 'O', 'A', 'Q', 'U', 'I', 'N');
        $limite = count($arrayLetras) - 1;
        $num = mt_rand(0, $limite);
        for ($i = 1; $i <= $num; $i++) {
            $resultado = base64_encode($resultado);
        }
        $resultado = $resultado . '+' . $arrayLetras[$num];
        $resultado = base64_encode($resultado);
        return $resultado;
    }
    protected function decodificar($dato) {
        $resultado = base64_decode($dato);
        list($resultado, $letra) = explode('+', $resultado);
        $arrayLetras = array('J', 'O', 'A', 'Q', 'U', 'I', 'N');
        for ($i = 0; $i < count($arrayLetras); $i++) {
            if ($arrayLetras[$i] == $letra) {
                for ($j = 1; $j <= $i; $j++) {
                    $resultado = base64_decode($resultado);
                }
                break;
            }
        }
        return $resultado;
    }
}   

