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
    public function getActiveUsers($loginUser) {
        $loginData = $loginUser->findAll();
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
    public function getAllPosts(object $userPost, object $postLike, object $postComment) {
        $postData = $userPost->findAll();
        $posts = [];
        for ($i = count($postData)-1; $i >=0 ; $i--) {
            $id = $postData[$i]->getId();

            $allComment = $postComment->findBy(['postid' => $id]);
            $comment = [];
            for ($j = count($allComment)-1; $j >=0 ; $j--) {
                array_push($comment, $allComment[$j]->getComments());
            }
            $allLike = $postLike->findBy(['postid' => $id]);
            $name = $postData[$i]->getUsername();
            $post = $postData[$i]->getPost();
            $like = count($allLike);
            $comments = count($allComment);
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