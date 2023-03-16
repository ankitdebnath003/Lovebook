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
use App\Services\FormData;
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
     *   @var object
     *     stores the object of the Entity Manager Interface Class.
     */
    public $em;

    /**
     * Constructor is used to set the values in class variables.
     * 
     *   @param object $em
     *     Stores the object of Entity Manager Interface Class.
     * 
     *   @return void
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * This method is used to send the user to the sign up page.
     * 
     * @Route("/register", name="register")
     * When the user clicks on sign up then this route is used.
     * 
     *   @return Response
     */
    public function register(): Response
    {
        return $this->render('register/index.html.twig');
    }

    /**
     * This method is used to send the user to the main page of the controller.
     * 
     * @Route("/main", name="main")
     * When the user sets the route to main then this route is used.
     * 
     *   @return Response
     */
    public function main(SessionInterface $si, Request $rq, EntityManagerInterface $em): Response
    {
        $n = NULL;
        echo $n;
        if($n) {
            echo "null";
        }
        echo "not null";
        return $this->render('main/index.html.twig');
    }

    /**
     * This function is used to check if the user already logged in or not if the
     * user already logged in then send the user to the main page otherwise send
     * the user to the login page.
     * 
     * @Route("/login", name = "login")
     * When the user try to login to the main page this route is used.
     * 
     *   @param object $si
     *     Stores the object Session Interface Class.
     * 
     *   @return Response
     */
    public function login(SessionInterface $si): Response
    {
        if ($si->get('username')) {
            $form = new FormData($this->em);
            $loginUser = $form->getActiveUsers();
            $posts = $form->getAllPosts();
            $si->set('postdata',$posts);
            $si->set('loginuser',$loginUser);
            return $this->render('form/index.html.twig',[
                "username" => $si->get('username'),
                "postdata" => $si->get('postdata'),
                "logindata" => $si->get('loginuser')
            ]);
        }
        return $this->render('login/index.html.twig',[
            "flag" => 1
        ]);
    }

    /**
     * This function is used to send the user to the forgot password page.
     * 
     * @Route("/forget", name = "forget")
     * When the user clicks on forgot password this route is used.
     * 
     *   @return Response
     */
    public function forget(): Response
    {
        return $this->render('forget/index.html.twig',[
            "flag" => 2
        ]);
    }
}