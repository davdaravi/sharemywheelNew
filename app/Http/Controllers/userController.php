<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\userRepository;
use App\Http\Requests;

class userController extends Controller
{
    //this function is for getting user details
    public function getUserDetails(userRepository $userRepository)
    {
        return $userRepository->getUserDetails();
    }
    //this function is for updating user personal information
    public function updateUserDetails(userRepository $userRepository)
    {
    	return $userRepository->updateUserDetails();
    }
    //this function is for email confirmation
    public function emailConfirmation(userRepository $userRepository)
    {
        return $userRepository->emailConfirmation();
    }
    //function for mobile confirmation
    public function mobileConfirmation(userRepository $userRepository)
    {
        return $userRepository->mobileConfirmation();
    }
    //save user preference
    public function savePreference(userRepository $userRepository)
    {
        return $userRepository->savePreference();
    }
    //function for user image upload
    public function imageUpload(userRepository $userRepository)
    {
        return $userRepository->imageUpload();
    }
    //function for licence image upload
    public function licenceUpload(userRepository $userRepository)
    {
        return $userRepository->licenceUpload();
    }
    //function for send email verification code
    public function sendEmailCode(userRepository $userRepository)
    {
        return $userRepository->sendEmailCode();
    }
    //function for send mobile verification code
    public function sendMobileCode(userRepository $userRepository)
    {
        return $userRepository->sendMobileCode();
    }
    //function for get details of user
    public function getProfile(userRepository $userRepository,$id,$rideid)
    {
        return $userRepository->getProfile($id,$rideid);
    }
    //function for request withdraw amount
    public function withdrawAmount(userRepository $userRepository)
    {
        return $userRepository->withdrawAmount();
    }
    public function changePassword(userRepository $userRepository)
    {
        return $userRepository->changePassword();
    }
    public function forgotPassword(userRepository $userRepository)
    {
        return $userRepository->forgotPassword();
    }
    public function getCityName(userRepository $user)
    {
        return $user->getCityName();
    } 
    public function latest_news(userRepository $user)
    {
        return $user->latest_news();
    }
    public function coupanCode(userRepository $user)
    {
        return $user->coupanCode();
    }
}
