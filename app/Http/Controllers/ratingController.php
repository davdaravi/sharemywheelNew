<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Repositories\ratingRepository;

class ratingController extends Controller
{
    //
    public function getExchangeRating(ratingRepository $ratingRepository)
    {
    	return $ratingRepository->getExchangeRating();
    }
    public function ratingExchange(ratingRepository $ratingRepository)
    {
    	return $ratingRepository->ratingExchange();
    }
    //this function is for fetch data in rating given table
    public function ratingGiven(ratingRepository $ratingRepository)
    {
    	return $ratingRepository->ratingGiven();
    }
    //this function is for fetch data in rating received table
    public function ratingReceived(ratingRepository $ratingRepository)
    {
        return $ratingRepository->ratingReceived();
    }
}
