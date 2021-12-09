<?php


namespace App\Event;


use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber
{
    /**
     * @var UserRepository
     */
    private UserRepository $user;

    public function __construct(UserRepository $user){
        $this->user = $user;
    }
    public function updateJwtData(JWTCreatedEvent $event)
    {
        // On enrichit le data du Token
        $data = $event->getData();
        $res = $this->user->findBy(['telephone'=>$data['username']]);
        $isAbonne = $res[0]->getDebutAbonnement()!=null;
        $data['status'] =$res[0]->getIsArchivate();
        $data['id'] =  $res[0]->getId();
        $data['isArchived'] =  $res[0]->getIsArchivate();
        $data['isAbonner'] =  $isAbonne;
        $event->setData($data);
    }
}