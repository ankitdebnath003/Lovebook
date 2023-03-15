<?php

namespace App\Entity;

use App\Repository\UserotpRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserotpRepository::class)]
class Userotp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?int $otp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setUsername(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOtp(): ?int
    {
        return $this->otp;
    }

    public function setOtp(?int $otp): self
    {
        $this->otp = $otp;

        return $this;
    }

    public function setUserOtp(string $email, int $otp) {
        $this->email = $email;
        $this->otp = $otp;
    }
}
