<?php declare(strict_types=1);

namespace App\Entity\Traits;

use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait IdTrait
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     */
    private $id;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function generateId(): self
    {
        try {
            $this->id = Uuid::uuid4()->toString();
        } catch (UnsatisfiedDependencyException | \Exception $e) {
            throw new HttpException(500, 'Caught exception: ' . $e->getMessage());
        }

        return $this;
    }
}
