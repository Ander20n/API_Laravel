<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function register(Request $request): JsonResponse
    {  
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:4',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('A validação falhou!', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'Usuário registrado com sucesso.');
    }

    public function login(Request $request): JsonResponse
    {
        if(Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::guard('web')->user();
            $success['token'] = $user->createToken('MyApp')-> accessToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'Usuário logado com sucesso.');
        }
        else{
            return $this->sendError('Não autorizado!', ['error'=>'Não autorizado']);
        }
    }

}