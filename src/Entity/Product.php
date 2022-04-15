<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sku;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $reviews_count;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=Seller::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $seller_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): ?int
    {
        return $this->sku;
    }

    public function setSku(int $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getReviewsCount(): ?int
    {
        return $this->reviews_count;
    }

    public function setReviewsCount(int $reviews_count): self
    {
        $this->reviews_count = $reviews_count;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }


    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        //$this->created_at = $created_at;
        $this->created_at = new \DateTimeImmutable();

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    /**
     * @ORM\PrePersist
     */
    public function setUpdatedAtValue()
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getSellerId(): ?Seller
    {
        return $this->seller_id;
    }

    public function setSellerId(?Seller $seller_id): self
    {
        $this->seller_id = $seller_id;

        return $this;
    }
}
