<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\rideRepository;
use App\Http\Requests;

class rideController extends Controller
{
    //
    public function rideSearch(rideRepository $rideRepository)
    {
    	return $rideRepository->rideSearch();
    }
    public function findRideSearch(rideRepository $rideRepository)
    {
        return $rideRepository->findRideSearch();
    }
    public function rideListSearch(rideRepository $rideRepository)
    {
    	return $rideRepository->rideListSearch();
    }
    public function getRideData(rideRepository $rideRepository)
    {
    	return $rideRepository->getRideData();
    }

    public function carList(rideRepository $rideRepository){
        return $rideRepository->carList();
    }
    //this function is for getting ride details
    public function getRideDetail($id,rideRepository $rideRepository)
    {
        return $rideRepository->getRideDetail($id);
    }
    public function getRideList(rideRepository $rideRepository)
    {
        return $rideRepository->getRideList();
    }
    //this function is for get ride offer details
    public function getRideOffer(rideRepository $rideRepository,$id)
    {
        return $rideRepository->getRideOffer($id);
    }
    //this will give you ride details
    public function getRideInfo(rideRepository $rideRepository)
    {
        return $rideRepository->getRideInfo();
    }
    //function for book ride
    public function bookRide(rideRepository $rideRepository)
    {
        return $rideRepository->bookRide();
    }
    //ride book using ccavenu
    public function bookCcavenu(rideRepository $rideRepository)
    {
        return $rideRepository->bookCcavenu();
    }

    public function ccavenuFeedback(rideRepository $rideRepository)
    {
        return $rideRepository->ccavenuFeedback();
    }

    public function findride(rideRepository $rideRepository)
    {
        return $rideRepository->findride();
    }

    public function statusMessage(rideRepository $rideRepository)
    {
        return $rideRepository->statusMessage();
    }
    public function bookedRidePaymentMessage(rideRepository $rideRepository)
    {
        return $rideRepository->bookedRidePaymentMessage();
    }
}
