<?php

namespace App\Services;

use App\Entity\PostComment;
use App\Entity\UserLogin;
use App\Entity\UserPost;
use App\Entity\PostLike;

class FormData
{
        
    /**
     *   @var object
     *     Stores the object of the Entity Manager Interface Class.
     */
    public $em;

    /**
     * This constructor is used to set the object of Entity Manager Interface class
     * to the class variable.
     * 
     *   @param object $em
     *     Stores the object of the Entity Manager Interface Class.
     */
    public function __construct(object $em)
    {
        $this->em = $em;
    }
    
    /**
     * This method is used to get all the active users.
     * 
     *   @return array
     *     Send all the active users.
     */
    public function getActiveUsers() {
        $loginData = $this->em->getRepository(UserLogin::class)->findAll();
        $loginUser = [];
        for ($i = count($loginData)-1; $i >= 0; $i--) {
            if ($loginData[$i]->getIsLogin() == "YES") {
                array_push($loginUser, $loginData[$i]->getUsername());
            }
        }
        return $loginUser;
    }
    
    /**
     * This method is used to get all the posts details i.e., Likes,Comments
     * of all posts.
     * 
     *   @return array
     *     Send all the Post details.
     */
    public function getAllPosts() {
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
        return $posts;
    }
}