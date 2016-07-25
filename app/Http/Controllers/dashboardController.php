<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\dashboardRepository;
class dashboardController extends Controller
{
    //
    public function viewDashboard(dashboardRepository $dashboardRepository)
    {
    	return $dashboardRepository->viewDashboard();
    }
    //function for list of paid transaction
    public function paidTransaction(dashboardRepository $dashboardRepository)
    {
    	return $dashboardRepository->paidTransaction();
    }
    //function for list of earned transaction
    public function earnTransaction(dashboardRepository $dashboardRepository)
    {
    	return $dashboardRepository->earnTransaction();
    }
    //get wallet amount
    public function amount(dashboardRepository $dashboardRepository)
    {
        return $dashboardRepository->getAmount();
    }
    //function for get ridebook history
    public function rideBookHistory(dashboardRepository $dashboardRepository)
    {
        return $dashboardRepository->rideBookHistory();
    }
    //function for add coupan code
    public function addCoupan(dashboardRepository $dashboardRepository)
    {
        return $dashboardRepository->addCoupan();
    }
}
