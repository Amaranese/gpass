<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\User;
class LoginController extends Controller
{
    public function login(Request $req)
    {
        if (!isset($_POST['email']) or !isset($_POST['password'])) 
        {
            return $this->error(400, 'Campos vacios');
        }
        $email = $_POST['email'];
        $password = $_POST['password'];
        if ($this->checkIsRegister($email,$password))
        {
            $userSave = User::where('email', $email)->first();
            $userData = array(
                'id' => $userSave->id,
                'name' => $userSave->name,
                'email' => $userSave->email,
                'password' => $userSave->password
            );
            $token = JWT::encode($userData, $this->key);
            return $this->success('El usuario se ha loguado de manera satisfactoria', $token);
        }
        else
        {
            return $this->error(400, 'Los parametros introducidos no son correctos, por favor introduzca los parametros correctos');
        }
    }
    public function checkIsRegister($email,$password)
    {   
        $userSave = User::where('email', $email)->first();
        $passwordSave = $this->decodificar($userSave->password);
        if(!is_null($userSave) && $passwordSave == $password)
        {
            return true;
        }
        return false;
    }
}