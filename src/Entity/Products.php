<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductsRepository")
 */
class Products
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sku;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Suppliers", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brands", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductTypes", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $compare;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $barcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $supplierStock;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stock;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Images", mappedBy="products")
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $length;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $season;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductSizes", inversedBy="products")
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductColors", inversedBy="products")
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $women;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $men;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $girls;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $boys;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $unisex;

    /**
     * @ORM\Column(type="boolean")
     */
    private $toShopify;

    /**
     * @ORM\Column(type="boolean")
     */
    private $massUploadedRex;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shopifyProductId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shopifyVariantId;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $syncWithShopify;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getSupplier(): ?Suppliers
    {
        return $this->supplier;
    }

    public function setSupplier(?Suppliers $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getBrand(): ?Brands
    {
        return $this->brand;
    }

    public function setBrand(?Brands $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?ProductTypes
    {
        return $this->type;
    }

    public function setType(?ProductTypes $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCompare(): ?string
    {
        return $this->compare;
    }

    public function setCompare(?string $compare): self
    {
        $this->compare = $compare;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getSupplierStock(): ?string
    {
        return $this->supplierStock;
    }

    public function setSupplierStock(string $supplierStock): self
    {
        $this->supplierStock = $supplierStock;

        return $this;
    }

    public function getStock(): ?string
    {
        return $this->stock;
    }

    public function setStock(string $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProducts($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProducts() === $this) {
                $image->setProducts(null);
            }
        }

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(?string $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(?string $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getSize(): ?ProductSizes
    {
        return $this->size;
    }

    public function setSize(?ProductSizes $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getColor(): ?ProductColors
    {
        return $this->color;
    }

    public function setColor(?ProductColors $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getWomen(): ?string
    {
        return $this->women;
    }

    public function setWomen(?string $women): self
    {
        $this->women = $women;

        return $this;
    }

    public function getMen(): ?string
    {
        return $this->men;
    }

    public function setMen(?string $men): self
    {
        $this->men = $men;

        return $this;
    }

    public function getGirls(): ?string
    {
        return $this->girls;
    }

    public function setGirls(?string $girls): self
    {
        $this->girls = $girls;

        return $this;
    }

    public function getBoys(): ?string
    {
        return $this->boys;
    }

    public function setBoys(?string $boys): self
    {
        $this->boys = $boys;

        return $this;
    }

    public function getUnisex(): ?string
    {
        return $this->unisex;
    }

    public function setUnisex(?string $unisex): self
    {
        $this->unisex = $unisex;

        return $this;
    }

    public function getToShopify(): ?bool
    {
        return $this->toShopify;
    }

    public function setToShopify(bool $toShopify): self
    {
        $this->toShopify = $toShopify;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMassUploadedRex()
    {
        return $this->massUploadedRex;
    }

    /**
     * @param mixed $massUploadedRex
     * @return Products
     */
    public function setMassUploadedRex(bool $massUploadedRex): self
    {
        $this->massUploadedRex = $massUploadedRex;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShopifyProductId()
    {
        return $this->shopifyProductId;
    }

    /**
     * @param mixed $shopifyProductId
     */
    public function setShopifyProductId($shopifyProductId): void
    {
        $this->shopifyProductId = $shopifyProductId;
    }

    /**
     * @return mixed
     */
    public function getShopifyVariantId()
    {
        return $this->shopifyVariantId;
    }

    /**
     * @param mixed $shopifyVariantId
     */
    public function setShopifyVariantId($shopifyVariantId): void
    {
        $this->shopifyVariantId = $shopifyVariantId;
    }

    /**
     * @return mixed
     */
    public function getSyncWithShopify()
    {
        return $this->syncWithShopify;
    }

    /**
     * @param mixed $syncWithShopify
     * @return Products
     */
    public function setSyncWithShopify(bool $syncWithShopify): self
    {
        $this->syncWithShopify = $syncWithShopify;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return Products
     */
    public function setUpdatedAt($updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}
