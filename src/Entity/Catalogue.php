<?php

namespace App\Entity;

use App\Repository\CatalogueRepository;
use App\Trait\Entity\PricingLabelTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CatalogueRepository::class)]
class Catalogue extends AbstractEntity
{
    use PricingLabelTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createDate = null;

    #[ORM\Column]
    private ?int $itemCount = 0;

    #[ORM\ManyToOne(inversedBy: 'catalogues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'catalogue')]
    private Collection $items;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $pricingMin = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $pricingMax = '0.00';

    public function __construct()
    {
        $this->createDate = new \DateTime();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function getItemCount(): ?int
    {
        return $this->itemCount;
    }

    public function setItemCount(int $itemCount): static
    {
        $this->itemCount = $itemCount;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCatalogue($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCatalogue() === $this) {
                $item->setCatalogue(null);
            }
        }

        return $this;
    }

    public function getPricingMin(): ?string
    {
        return $this->pricingMin;
    }

    public function setPricingMin(?string $pricingMin): static
    {
        $this->pricingMin = $pricingMin;

        return $this;
    }

    public function getPricingMax(): ?string
    {
        return $this->pricingMax;
    }

    public function setPricingMax(?string $pricingMax): static
    {
        $this->pricingMax = $pricingMax;

        return $this;
    }

    public function recountValue(): void
    {
        $minValue = 0;
        $maxValue = 0;

        $items = $this->getItems();
        foreach ($items as $item) {

            if (!$item->hasPricing()) {
                continue;
            }

            if (!$item->getPricingMin()) {
                $minValue += $item->getPricingMax();
                $maxValue += $item->getPricingMax();
            } elseif (!$item->getPricingMax()) {
                $minValue += $item->getPricingMin();
                $maxValue += $item->getPricingMin();
            } else {
                $minValue += $item->getPricingMin();
                $maxValue += $item->getPricingMax();
            }
        }

        $this->setPricingMin($minValue);
        $this->setPricingMax($maxValue);
    }
}
