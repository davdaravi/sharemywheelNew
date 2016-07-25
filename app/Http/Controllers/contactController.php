<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Repositories\contactRepository;
class contactController extends Controller
{
    //function for contact of user //method post
    public function contact(contactRepository $contact)
    {
    	return $contact->contact();
    }
}
