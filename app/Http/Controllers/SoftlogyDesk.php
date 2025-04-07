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
        return $this->softogyAuthServices->Authenticate($data);
    }

    public function reportStatusStore(Request $request){
        $data = $request->all();
        return $this->softogyservices->reportStatusStore($data);
    }
}
