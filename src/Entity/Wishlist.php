<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WishlistRepository")
 */
class Wishlist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $customerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ProductIds;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getProductIds(): ?string
    {
        return $this->ProductIds;
    }

    public function setProductIds(?string $ProductIds): self
    {
        $this->ProductIds = $ProductIds;

        return $this;
    }
}
