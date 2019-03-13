<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Password;

class PasswordController extends Controller
{
    const SECRETKEY = "JOAQUIN";
    public function index()
    {
        
        if($this->checkLogin()) 
        {
            $userData = $this->getUserData();         
            $password = Password::where('user_id', $userData->id)->get();

            $passwordArray = [];

            foreach($password as $key => $password)
            {    
                array_push($passwordArray, $password);
            }
            
            if(count($passwordArray) == 0)
            {
                return $this->error(400, "No existen contraseñas");
            }

            return $this->success('Contraseñas creadas', $passwordArray);
            
        }else
        {
            return $this->error(400, "Acceso denegado");
        }
    }

    public function store(Request $request)
    {
        if($this->checkLogin())
        {
            if (!isset($_POST['passwordName']) or !isset($_POST['password']) or !isset($_POST['category_id'])) 
            {
                return $this->error(400, 'Campos vacíos');
            }

            $userData = $this->getUserData();        
            $newCategory_idName = $request->passwordName;
            $newCategory_id = $request->password;
            $category_id = $request->category_id;
            $user_id = $userData->id;

            if(!$this->IsUsedName($user_id,$newCategory_idName))
            {
                 return $this->error(400, 'ya existe una contraeña con ese nombre');
            }

            $password = new Password();
            $password->title = $this->deleteAllSpace($newCategory_idName);
            $password->password = $this->codificar($newCategory_id);
            $password->category_id = $category_id;
            $password->user_id = $user_id;
            $password->save();

            return $this->success('Contraseña creada', "");

        }else {

            return $this->error(400, "Acceso denegado");
        }
            
    }


    public function IsUsedName($id , $passwordName)
    {
        $password = Password::where('user_id', $id)->get();

        foreach ($password as $key => $password) 
        {
            if($password->title == $passwordName)
            {
                return false;
            }
        }
        return true;
    }
  
    public function update(Request $request, $id)
    {              
       if ($this->checkLogin()) 
        { 
            $userData = $this->getUserData();

            $password = Password::where('id', $id)->first();

            if(!isset($_POST['newName']) && !is_null($request->newName))
            {
                if(!$this->IsUsedName($userData->id,$this->deleteAllSpace($request->newName)))
                {
                     return $this->error(400, 'Ya existe esta contraseña');
                }

                $password->title = $this->deleteAllSpace($request->newName);
            }
            if(!isset($_POST['newPassword']) && !is_null($request->newPassword))
            {
                $password->password =  $this->codificar($request->newPassword);
            }
            if(!isset($_POST['newCategory_id']) && !is_null($request->newCategory_id))
            {
                $password->category_id = $request->newCategory_id;
            }

            $password->save();

            return $this->success('Contraseña modificado', $password);
        }else
        {
            return $this->error(400, "Acceso denegado");
        }
    }
   
    public function destroy($id)
    {
        if($this->checkLogin())
       {

            $password = Password::where('id', $id)->first();

            if(!is_null($password))
            {
                $password->delete();
                return $this->success('Contraseña eliminada', "");

            }else
            {
                return $this->error(400, "No existe esta contraseña");
            }


       }else
       {
            return $this->error(400, "Acceso denegado");
       }           
    }
}
