<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Tag
{
    use IdTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function __construct()
    {
        if (null == $this->id) {
            $this->generateId();
        }
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
}
