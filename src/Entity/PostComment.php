<?php

namespace App\Entity;

use App\Repository\PostCommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostCommentRepository::class)]
class PostComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $postid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comments = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostid(): ?int
    {
        return $this->postid;
    }

    public function setPostid(?int $postid): self
    {
        $this->postid = $postid;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }
}
