<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Contracts\SoftlogyDeskServicesInterface;


class SoftlogyDesk extends Controller
{
    protected $softogyservices;
    public function __construct(SoftlogyDeskServicesInterface $softogyservices)
    {
        $this->softogyservices=$softogyservices;
    }

    public function authToken(){
        return $this->softogyservices->getAuthToken();
    }
}
