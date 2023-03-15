<?php

namespace App\Controller;

use App\Entity\PostComment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\FormValidator;
use App\Services\OtpManager;
use App\Entity\UserLogin;
use App\Entity\Username;
use App\Entity\Userotp;
use App\Entity\UserPost;
use App\Entity\PostLike;
use Pusher\Pusher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(): Response
    {
        return $this->render('register/index.html.twig');
    }

    /**
     * @Route("/main", name="main")
     */
    public function main(Request $rq, EntityManagerInterface $em): Response
    {
        $id = $_ENV['PUSHER_APP_ID'];
        $key = $_ENV['PUSHER_APP_ID'];
        $secret = $_ENV['PUSHER_APP_ID'];
        $cluster = $_ENV['PUSHER_APP_ID'];
        $data = [
            'id' => $id,
            'key' => $key,
           'secret' => $secret,
            'cluster' => $cluster,
        ];
        dd($data);
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/login", name = "login")
     */
    public function login(): Response
    {
        return $this->render('login/index.html.twig',[
            "flag" => 1
        ]);
    }

    /**
     * @Route("/forget", name = "forget")
     */
    public function forget(): Response
    {
        return $this->render('forget/index.html.twig',[
            "flag" => 2
        ]);
    }
}