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

class FormController extends AbstractController
{
    /**
     *   @var object
     *     stores the object of the Entity Manager Interface Class.
     */
    private $em;
    /**
     *   @var string
     *     stores the id of pusher.
     */
    private $id;
    /**
     *   @var string
     *     stores the jey of pusher.
     */
    private $key;
    /**
     *   @var string
     *     stores the secret of pusher.
     */
    private $secret;
    /**
     *   @var string
     *     stores the cluster of pusher.
     */
    private $cluster;

    /**
     * Constructor is used to set the values in class variables.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->id = $_ENV['PUSHER_APP_ID'];
        $this->key = $_ENV['PUSHER_KEY'];
        $this->secret = $_ENV['PUSHER_SECRET'];
        $this->cluster = $_ENV['PUSHER_CLUSTER'];
    }
    
    /**
     * This function is used to validate the login information of the user.
     * 
     * @Route("/loginform", name = "loginform")
     * If login data is valid then redirect the user to the main page otherwise
     * show the error to the user.
     * 
     *   @var string $uName
     *     Stores the username.
     *   @var string $pass
     *     Stores the password.
     * 
     *   @return Response
     *     Based on valid username and password.
     */
    public function loginform(SessionInterface $si, Request $rq): Response
    {
        $uName = $rq->get("username");
        $pass = $rq->get("password");

        $user = $this->em->getRepository(Username::class)->findOneBy(['username' => $uName]);
        $msg = 1;
        $flag = 3;

        if ($user != NULL) {
            $checkPass = $user->getPassword();
            if (!password_verify($pass, $checkPass)) {
                $msg = "Incorrect Password";
                $flag = 2;
            }
        }
        else {
            $msg = "Username Not Found";
            $flag = 2;
        }

        if ($msg != 1) {
            return $this->render('login/index.html.twig',[
                "msg" => $msg,
                "flag" => $flag
            ]);
        }

        $login = new UserLogin();
        $user = $this->em->getRepository(UserLogin::class)->findOneBy(["username" => $uName]);
        if ($user != NULL) {
            $user->setisLogin("YES");
            $this->em->persist($user);
            $this->em->flush();
        }
        else {
            $login->setUsername($uName);
            $login->setIsLogin("YES");
            $this->em->persist($login);
            $this->em->flush();
        }

        // Get the already login users.
        $loginData = $this->em->getRepository(UserLogin::class)->findAll();
        $loginUser = [];
        for ($i = count($loginData)-1; $i >= 0; $i--) {
            if ($loginData[$i]->getIsLogin() == "YES") {
                array_push($loginUser, $loginData[$i]->getUsername());
            }
        }
        
        // Get all the posts.
        $postData = $this->em->getRepository(UserPost::class)->findAll();
        $posts = [];
        for ($i = count($postData)-1; $i >=0 ; $i--) {
            $id = $postData[$i]->getId();

            $postComment = $this->em->getRepository(PostComment::class)->findBy(['postid' => $id]);
            $comment = [];
            for ($j = count($postComment)-1; $j >=0 ; $j--) {
                array_push($comment, $postComment[$j]->getComments());
            }
            $postLike = $this->em->getRepository(PostLike::class)->findBy(['postid' => $id]);
            $name = $postData[$i]->getUsername();
            $post = $postData[$i]->getPost();
            $like = count($postLike);
            $comments = count($postComment);
            $arr = [
                'id' => $id,
                'user' => $name,
                'post' => $post,
                'likes' => $like,
                'comments' => $comments,
                'comment' => $comment
            ];            
            array_push($posts, $arr);
        }

        return $this->render('form/index.html.twig',[
            "username" => $uName,
            "postdata" => $posts,
            "logindata" => $loginUser
        ]);
    }

    /**
     * The function is used to get the Signup Page data and validate it and if
     * the data is validated then store the data of the user.
     * 
     * @Route("/signup", name = "signup") 
     * When register form is submitted this route is used.
     * 
     *   @var string $fName
     *     Stores the firstname of the user.
     *   @var string $lName
     *     Stores the lastname of the user.
     *   @var string $uName
     *     Stores the username of the user.
     *   @var string $email
     *     Stores the email of the user.
     *   @var string $otp
     *     Stores the otp.
     *   @var string $pass
     *     Stores the password.
     *   @var string $cPass
     *     Stores the confirm password of the user.
     * 
     *   @return Response
     */
    public function signup(Request $rq): Response
    {
        $fName = $rq->get("firstname");
        $lName = $rq->get("lastname");
        $uName = $rq->get("username");
        $email = $rq->get("email");
        $otp = $rq->get("otp");
        $pass = $rq->get("password");
        $cPass = $rq->get("confirmpassword");
        $obj = new FormValidator($fName, $lName, $uName, $email, $otp, $pass, $cPass, $this->em);
        $msg = $obj->validateForm();
        $flag = TRUE;

        // If msg is not 1 then there is some validation error in the form.
        if ($msg == 1) {
            $username = new Username();
            $username->setUserDetails($fName, $lName, $uName, $email, $pass);
            $this->em->persist($username);
            $this->em->flush();
        }
        else {
            $flag = FALSE;
        }

        return $this->render('register/signup.html.twig', [
            'flag' => $flag,
            'msg' => $msg
        ]);
    }

    /**
     * This is a ajax function used to send otp to the user.
     * 
     * @Route("/ajax", name = "ajax")
     * This route is used for ajax call for sending otp.
     * 
     *   @var string $email
     *     Stores the email id of the user.
     * 
     *   @return Response
     *     based on otp is sent or not.
     */
    public function ajaxAction(Request $rq): Response
    {
        if ($rq->isXmlHttpRequest()) {  
            $email = $rq->get('emailid');
            $mailPass = $this->getParameter('app.mailpassword');
            $otp = new OtpManager($mailPass, $this->em);
            $f = TRUE;
            while ($f) {
                $otpno = random_int(100000,999999);
                if (!$otp->checkOtp($otpno)) {
                    $flag = $otp->checkEmail($email);
                    $otp->setOtp($email, $otpno, $flag);
                    $otp->sendOtp($email, $otpno);
                    $f = FALSE;
                }
            } 
            return new Response("Mail Has been sent"); 
        } 
        else { 
             return new Response("Mail Can't be sent"); 
        } 
    }

    /**
     * This function is used to show the user that the username is available or 
     * not on the signup page.
     * 
     * @Route("/availability", name = "availability")
     * This route is used for ajax call for username availability.
     * 
     *   @var string $uName
     *     Stores the username of the user to check the availability.
     * 
     *   @return Response
     *     Based on the availability of the username.
     */
    public function availabilityAction(Request $rq): Response 
    {
        if ($rq->isXmlHttpRequest()) {
            $uName = $rq->get('username');
            $user = $this->em->getRepository(Username::class)->findOneBy(['username' => $uName]);
            if ($user != NULL) {
                return new Response(FALSE);
            }
            else {
                return new Response(TRUE);
            }
        }
    }

    /**
     * This is used to get the email id from the forgot password link.
     * 
     * @Route("/changepass/{email}", name = "changepass")
     * When click on forgot password link then this route is used.
     * 
     *   @var string $email
     *     Stores the email id of the user.
     * 
     *   @return Response
     *     Return the user to the forgot password page.
     */
    public function changepass(Request $rq): Response
    {
        $email = $rq->get('email');
        return $this->render('forget/password.html.twig',[
            "email" => $email,
            "flag" => 3
        ]);
    }

    /**
     * It is used to get the new password from the user and set it to the entity.
     * 
     * @Route("/passchange/{email}", name = "passchange")
     * When the clicked on submit after giving new password then this route is used.
     * 
     *   @var string $email
     *     Stores the email id of the user.
     *   @var string $pass
     *     Stores the password.
     *   @var string $cPass
     *     Stores the confirm password.
     * 
     *   @return Response
     *     Based on new valid password.
     */
    public function passchange(Request $rq): Response 
    {
        $email = $rq->get("email");
        $pass = $rq->get("password");
        $cPass = $rq->get("confirmpassword");
        if ($cPass != $pass) {
            return $this->render('forget/password.html.twig',[
                "msg" => "Incorrect Password",
                "flag" => 1,
                "email" => $email
            ]);
        }
        $upperCase = preg_match('@[A-Z]@', $pass);
        $lowerCase = preg_match('@[a-z]@', $pass);
        $number = preg_match('@[0-9]@', $pass);
        $specialChars = preg_match('@[^\w]@', $pass);
        
        if (strlen($pass) < 8 or !$upperCase or !$lowerCase or !$number or !$specialChars) {
            return $this->render('forget/password.html.twig',[
                "msg" => "Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.",
                "flag" => 1,
                "email" => $email
            ]);
        }
        $decry = urldecode(base64_decode($email));
        $user = $this->em->getRepository(Username::class)->findOneBy(['email' => $decry]);
        $encry = password_hash($pass, PASSWORD_BCRYPT);
        $user->setPassword($encry);
        $this->em->persist($user);
        $this->em->flush();
        return $this->render('forget/password.html.twig',[
            "flag" => 2,
            "msg" => "Password Has Been Changed.",
            "email" => $email
        ]);
    }

    /**
     * This is used to logout the user and update data in the entity.
     * 
     * @Route("/logout/{username}", name = "logout")
     * When clicked on logout button this route is used.
     * 
     *   @var string $userName
     *     Stores the username.
     * 
     *   @return Response
     *     Redirect the user to the login page.
     */
    public function logout(Request $rq): Response
    {
        $userName = $rq->get("username");
        $user = $this->em->getRepository(UserLogin::class)->findOneBy(['username' => $userName]);
        $user->setisLogin("NO");
        $this->em->persist($user);
        $this->em->flush();
        return $this->render('login/index.html.twig',[
            "flag" => 3,
        ]);
    }

    /**
     * This is used to send forgot password link to the user's email id.
     * 
     * @Route("/forgot", name = "forgot")
     * When the user clicked on submit on the forgot password page then this 
     * route is used.
     * 
     *   @var string $email
     *     Stores the email id of the user.
     * 
     *   @return Response
     *     Based on valid email id.
     */
    public function forgot(Request $rq): Response
    {
        $email = $rq->get("email");
        $user = $this->em->getRepository(Userotp::class)->findOneBy(['email' => $email]);
        if ($user == NULL) {
            return $this->render('forget/index.html.twig',[
                "flag" => 1,
                "msg" => "Email Id Not Found"
            ]);
        }
        $mailPass = $this->getParameter('app.mailpassword');
        $otp = new OtpManager($mailPass, $this->em);
        $encry = urlencode(base64_encode($email));
        $link = "ankit.net/changepass/" . $encry;
        $otp->sendMail($email, $link);
        return $this->render('forget/index.html.twig',[
            "flag" => 3,
            "msg" => "Password Reset Link has been sent.",
            "email" => $email
        ]);
    }

    /**
     * @Route("/likes", name = "likes")
     */
    public function likesAction(Request $rq): Response
    {
        if ($rq->isXmlHttpRequest()) {  
            $uName = $rq->get('username');
            $pId = $rq->get('postid');
            $isLiked = $rq->get('like');
            if ($isLiked == "YES") {
                $like = new PostLike();
                $like->setPostid($pId);
                $like->setLikeBy($uName);
                $this->em->persist($like);
                $this->em->flush();
                
                $postLike = $this->em->getRepository(PostLike::class)->findBy(['postid' => $pId]);
                $data = [
                    "post" => $pId,
                    "like" => count($postLike)
                ];

                $pusher = new Pusher($this->key, $this->secret, $this->id, ['cluster' => $this->cluster]);
                $pusher->trigger('demo_pusher', 'updateLike', $data);

                return new Response("Like DONE");   
            }
            else {
                $del = $this->em->getRepository(PostLike::class)->findOneBy(['likeBy' => $uName]);
                $this->em->remove($del);
                $this->em->flush();

                $postLike = $this->em->getRepository(PostLike::class)->findBy(['postid' => $pId]);
                $data = [
                    "post" => $pId,
                    "like" => count($postLike)
                ];

                $pusher = new Pusher($this->key, $this->secret, $this->id, ['cluster' => $this->cluster]);
                $pusher->trigger('demo_pusher', 'updateLike', $data);

                return new Response("Like UNDONE");
            }
        }
    }

    /**
     * This is used to add the post in the database.
     * 
     * @Route("/addPost", name = "addPost")
     * When the user clicked on the post button then this route is used.
     * 
     *   @var string $post
     *     Store the post.
     *   @var string $userName
     *     Store the username.
     * 
     *   @return Response
     */
    public function addPostAction(Request $rq): Response
    {
        $post = $rq->get("post");
        $userName = $rq->get("id");
        
        $script = htmlspecialchars($post, ENT_QUOTES); 

        $userPost = new UserPost();
        $userPost->setUsername($userName);
        $userPost->setPost($post);
        $this->em->persist($userPost);
        $this->em->flush();
        $getPost = $this->em->getRepository(UserPost::class)->findBy(['username' => $userName]);
        $c = count($getPost)-1;
        $postId = $getPost[$c]->getId();
        $data = [
            "post" => $script,
            "userId" => $userName,
            "postid" => $postId
        ];

        $pusher = new Pusher($this->key, $this->secret, $this->id, ['cluster' => $this->cluster]);
        $pusher->trigger('demo_pusher', 'addName', $data);
        return new JsonResponse($data);
        return new Response("SUCCESS");
    }

    /**
     * This is used to remove the active user after the user close it.
     * 
     * @Route("/removeActiveUser", name = "removeActiveUser")
     * When the user close the tab this route is used.
     * 
     *   @var string $userName
     *     Stores the username.
     * 
     *   @return Response
     */
    public function removeActiveUserAction(Request $rq): Response
    {

        $userName = $rq->get("userid");
        $data = [
            "userid" => $userName,
            "action" => "remove"
        ];

        $login = $this->em->getRepository(UserLogin::class)->findOneBy(['username' => $userName]);
        $login->setIsLogin("NO");
        $this->em->persist($login);
        $this->em->flush();

        $pusher = new Pusher($this->key, $this->secret, $this->id, ['cluster' => $this->cluster]);
        $pusher->trigger('demo_pusher', 'activeUser', $data);
        
        return new Response("OK");
    }

    /**
     * This is used to add active users to the active user's list.
     * 
     * @Route("/addActiveUser", name = "addActiveUser")
     * When a new user logged in this route is used.
     * 
     *   @var string $userName
     *      Stores the username.
     * 
     *   @return Response
     */
    public function addActiveUserAction(Request $rq): Response
    {
        $userName = $rq->get("userid");
        $login = $this->em->getRepository(UserLogin::class)->findOneBy(['username' => $userName]);
        $login->setIsLogin("YES");
        $this->em->persist($login);
        $this->em->flush();

        $data = [
            "userid" => $userName,
            "action" => "add"
        ];

        $pusher = new Pusher($this->key, $this->secret, $this->id, ['cluster' => $this->cluster]);
        $pusher->trigger('demo_pusher', 'activeUser', $data);
        
        return new Response("OK");
    }

    /**
     * This is used to load the likes of the post on which the user liked.
     * 
     * @Route("/getLikes", name = "getLikes")
     * When the user gets active this route is used to load the likes.
     * 
     *   @var string $uName
     *     Stores the username.
     *  
     *   @return Response
     */
    public function getLikesAction(Request $rq): Response
    {
        $uName = $rq->get("username");
        $post = $this->em->getRepository(PostLike::class)->findBy(['likeBy' => $uName]);
        $postLike = [];
        if ($post) {
            for ($i=0; $i < count($post); $i++) { 
                array_push($postLike, $post[$i]->getPostid());
            }
            return new JsonResponse(['likes' => $postLike]);
        }
        return new Response(FALSE);
    }

    
    /**
     * This is used to add comment to the post.
     * 
     * @Route("/addComment", name = "addComment")
     * When the use comment on a post then this route is used.
     * 
     *   @param object $rq
     *     Stores the object of Request class.
     * 
     *   @var string $postId
     *     Stores the id of the post.
     *   @var string $comment
     *     Stores the comment of the post.
     *   @var string $uName
     *     Stores the username.
     * 
     *   @return Response
     */
    public function addCommentAction(Request $rq): Response
    {
        $postId = $rq->get("postid");
        $comment = $rq->get("comment");
        $uName = $rq->get("uname");
        $post = new PostComment();
        $post->setComments($comment);
        $post->setPostid($postId);
        $this->em->persist($post);
        $this->em->flush();
        
        $comm = $this->em->getRepository(PostComment::class)->findBy(['postid' => $postId]);
        $commNo = count($comm);
        
        $data = [
            "comment" => $comment,
            "commentno" => $commNo,
            "username" => $uName,
            "postid" => $postId
        ];
        $pusher = new Pusher($this->key, $this->secret, $this->id, ['cluster' => $this->cluster]);
        $pusher->trigger('demo_pusher', 'add', $data);
        return new Response("comment added");
    }

    /**
     * This is used to delete the post.
     * 
     * @Route("/deletePost", name = "deletePost")
     * When the user delete the post then this route is used.
     * 
     *   @param object $rq
     *     Stores the object of Request class.
     * 
     *   @var string $postId
     *     Stores the id of the post.
     * 
     *   @return Response
     */
    public function deletePostAction(Request $rq): Response
    {

        $postId = $rq->get("id");
        $post = $this->em->getRepository(UserPost::class)->findOneBy(['id' => $postId]);
        $comment = $this->em->getRepository(PostComment::class)->findBy(['postid' => $postId]);
        for ($i = 0; $i < count($comment); $i++) { 
            $this->em->remove($comment[$i]);
        }
        $this->em->remove($post);
        $this->em->flush();
        $postId = 'post'.$postId;
        $pusher = new Pusher($this->key, $this->secret, $this->id, ['cluster' => $this->cluster]);
        $pusher->trigger('demo_pusher', 'deletepost', $postId);
        return new Response($postId);
    }

    /**
     * This is used to edit the post.
     * 
     * @Route("/editPost", name = "editPost")
     * When the user edit the post this route is called.
     * 
     *   @param object $rq
     *     Stores the object of Request class.
     * 
     *   @var string $postId
     *     Stores the id of the post.
     *   @var string $text
     *     Stores the edited text.
     * 
     *   @return Response
     */
    public function editPostAction(Request $rq): Response
    {
        $postId = $rq->get("id");
        $text = $rq->get("post");

        $post = $this->em->getRepository(UserPost::class)->findOneBy(['id' => $postId]);
        $post->setPost($text);
        $this->em->persist($post);
        $this->em->flush();
        $postId = 'post'.$postId;
        $data = [
            'id' => $postId,
            'text' => $text
        ];
        $pusher = new Pusher($this->key, $this->secret, $this->id, ['cluster' => $this->cluster]);
        $pusher->trigger('demo_pusher', 'editpost', $data);
        return new Response("edited");
    }

}