<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\loginRepository;
class loginController extends Controller
{
    //
    public function checkLogin(loginRepository $loginRepository)
    {
    	return $loginRepository->checkLogin();
    }
    public function getLogin(loginRepository $loginRepository)
    {
    	return $loginRepository->getLogin();
    }
    public function signup(loginRepository $loginRepository)
    {
    	return $loginRepository->signup();
    }
    public function loadhome(loginRepository $loginRepository)
    {
        return $loginRepository->loadhome();
    }
}
