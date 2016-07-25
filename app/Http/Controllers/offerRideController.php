<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Repositories\offerRideRepository;

class offerRideController extends Controller
{
    //
    public function viewOfferRide(offerRideRepository $offerRideRepository)
    {
    	return $offerRideRepository->viewOfferRide();
    }
    public function createRide(offerRideRepository $offerRideRepository)
    {
    	return $offerRideRepository->createRide();
    }
    public function ccavenuPayment(offerRideRepository $offerRideRepository)
    {
    	return $offerRideRepository->ccavenuPayment();
    }
}
