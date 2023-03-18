<?php

namespace App\Entity;

use App\Repository\UserPostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPostRepository::class)]
class UserPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $post = null;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    private ?int $comments = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $LikedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function setPost(?string $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getComments(): ?int
    {
        return $this->comments;
    }

    public function setComments(?int $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getLikedBy(): ?string
    {
        return $this->LikedBy;
    }

    public function setLikedBy(?string $LikedBy): self
    {
        $this->LikedBy = $LikedBy;

        return $this;
    }
}
