<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class RegisterController extends Controller
{
   public function register (Request $request)
    {
        if (!isset($_POST['user']) or !isset($_POST['email']) or !isset($_POST['password'])) 
        {
            return $this->error(400, 'Todos los campos tienen que estar rellenos, en caso de que tenga uno vacio rellenelo por favor');
        }
        $user = $this->deleteAllSpace($_POST['user']);
        $email = $_POST['email'];
        $password = $_POST['password'];
        if($this->checkPassword($password))
        {
            return $this->error(400,'La contraseña ha de tener mas de 8 caracteres');
        }
        if($this->checkEmail($email))
        {
            return $this->error(400,'El email introducido no es valido, pruebe con otro correo');
        }
        if($this->checkUserExist($email))
        {
            return $this->error(400,'eL usuario que usted ha introducido ya existe');
        }
        if (!empty($user) && !empty($email) && !empty($password))
        {
            $users = new User();
            $users->name = $user;
            $users->password = $this->codificar($password);
            $users->email = $email;
            $users->save();

            return $this->success('El usuario ha sido registrado de forma exitosa',"");                 
        }
        else
        {
            return $this->error(400,'No puede haber campos vacios');
        }    
    }
    public function checkPassword($password)
    {
        if(strlen($password) < 8)
        {
            return true;
        }
        return false;
    }
    public function checkEmail($email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return true;
        }
        return false;
    }
    public function checkUserExist($email)
    {
        $userData = User::where('email',$email)->first();
        if(!is_null($userData))
        {
            return true;
        }
        return false;
    }
}