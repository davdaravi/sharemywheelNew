<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Repositories\chatRepository;
use App\Http\Requests;

class chatController extends Controller
{
    //
    public function getMessage(chatRepository $chatRepository)
    {
    	return $chatRepository->getMessage();
    }
    //
    public function sendMessage(chatRepository $chatRepository)
    {
    	return $chatRepository->sendMessage();
    }
    public function isTyping(chatRepository $chatRepository)
    {
    	return $chatRepository->isTyping();
    }
    public function isNotTyping(chatRepository $chatRepository)
    {
    	return $chatRepository->isNotTyping();
    }
    public function retriveMessage(chatRepository $chatRepository)
    {
        return $chatRepository->retriveMessage();
    }
}
