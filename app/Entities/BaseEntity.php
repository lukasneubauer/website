<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * @ORM\MappedSuperclass()
 */
abstract class BaseEntity extends Kdyby\Doctrine\Entities\BaseEntity
{
    use Identifier;

    /**
     * @param  array|\Traversable             $values
     * @throws Nette\InvalidArgumentException
     */
    public function setValues($values)
    {
        if ($values instanceof \Traversable) {
            $values = iterator_to_array($values);
        } elseif (!is_array($values)) {
            throw new Nette\InvalidArgumentException(sprintf('Parameter must be an array, %s given.', gettype($values)));
        }

        foreach ($values as $key => $value) {
            $this->{$key} = $value !== '' ? $value : null;
        }
    }
}
