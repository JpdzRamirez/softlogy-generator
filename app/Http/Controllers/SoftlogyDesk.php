<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Contracts\SoftlogyDeskServicesInterface;
use App\Contracts\AuthServicesInterface;


class SoftlogyDesk extends Controller
{
    protected $softogyservices;
    protected $softogyAuthServices;
    public function __construct(
        SoftlogyDeskServicesInterface $softogyservices
        , AuthServicesInterface $softogyAuthServices)
    {
        $this->softogyservices=$softogyservices;
        $this->softogyAuthServices=$softogyAuthServices;
    }

    public function sessionSoftlogyDeskToken(){
        return $this->softogyservices->getSessionToken();
    }

    public function sessionSoftlogyMicroToken(Request $request){
        $data = $request->all();
        $data['username'] = $this->softogyAuthServices->makeDecrypt($data['username'])['desencripted'];
        $data['password'] = $this->softogyAuthServices->makeDecrypt($data['password'])['desencripted'];
        return $this->softogyAuthServices->Authenticate($data);
    }

    public function makeBycript(Request $request){
        $text = $request->input('text');
        $data=$this->softogyAuthServices->makeBycript($text);
        return response()->json($data);
    }

    public function makeDecrypt(Request $request){
        $text = $request->input('text');
        $data=$this->softogyAuthServices->makeDecrypt($text);
        return response()->json($data);
    }

    public function reportStatusStore(Request $request){
        $data = $request->all();
        return $this->softogyservices->reportStatusStore($data);
    }
}
