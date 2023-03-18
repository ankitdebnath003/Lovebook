<?php

namespace App\Entity;

use App\Repository\PostLikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostLikeRepository::class)]
class PostLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $postid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $likeBy = null;

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

    public function getLikeBy(): ?string
    {
        return $this->likeBy;
    }

    public function setLikeBy(?string $likeBy): self
    {
        $this->likeBy = $likeBy;

        return $this;
    }
}
