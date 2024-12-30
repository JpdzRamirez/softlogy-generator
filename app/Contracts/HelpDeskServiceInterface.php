<?php

namespace App\Contracts;

interface HelpDeskServiceInterface
{
    public function createLocation(array $location);
    public function createEntiti(array $data);

    public function getTicketsCount(int $idUser);

    public function getTicketsUser(int $idUser);
    
}