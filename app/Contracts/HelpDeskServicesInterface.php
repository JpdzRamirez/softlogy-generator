<?php

namespace App\Contracts;

interface HelpDeskServicesInterface
{
    public function createLocation(array $location);
    public function createEntiti(array $data);

    public function getTicketsCount(int $idUser);

    public function getTicketsUser(int $idUser, $ticketName, $ticketStatus, $ticketType, int $perPage);
    
    public function createTicket(array $ticketData);
    
}