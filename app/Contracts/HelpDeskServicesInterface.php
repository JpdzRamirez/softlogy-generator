<?php

namespace App\Contracts;

interface HelpDeskServicesInterface
{
    public function createLocation(array $location);
    public function createEntiti(array $data);

    public function getTicketsCount(int $idUser);

    public function getTicketsUser(int $idUser,int $ticketID, string $ticketName, int $ticketStatus, int $ticketType, int $perPage);
    
    public function createTicket(array $ticketData);

    public function getTicketInfo(int $ticketID, int $userID);

    public function createFollowup(int $ticketID, $user,string $message, $file);
    
}