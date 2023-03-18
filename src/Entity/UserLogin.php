<?php

namespace App\Entity;

use App\Repository\UserLoginRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserLoginRepository::class)]
class UserLogin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $isLogin = null;

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

    public function getIsLogin(): ?string
    {
        return $this->isLogin;
    }

    public function setIsLogin(?string $isLogin): self
    {
        $this->isLogin = $isLogin;

        return $this;
    }
}
