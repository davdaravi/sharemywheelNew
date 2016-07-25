<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Repositories\carRepository;
class carController extends Controller
{
    //this function is for add new car
    public function addCar(carRepository $carRepository)
    {
    	return $carRepository->addCar();
    }
    //this function is for get car details
    public function getCarDetails(carRepository $carRepository)
    {
    	return $carRepository->getCarDetails();
    }
    //this function is for update car details
    public function updateCar(carRepository $carRepository){
    	return $carRepository->updateCar();
    }
    //function for delete car
    public function deleteCar(carRepository $carRepository)
    {
    	return $carRepository->deleteCar();
    }
    //function for get car list
    public function carList(carRepository $carRepository)
    {
    	return $carRepository->carList();
    }
}
