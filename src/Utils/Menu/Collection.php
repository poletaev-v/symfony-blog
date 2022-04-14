<?php

namespace App\Utils\Menu;

class Collection
{
    /**
     * @var CategoryTree[] $collection
     */
    private array $collection = [];

    public function push(CategoryTree $item): void
    {
        $this->collection[$item->getValue()->getId()] = $item;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function isEmpty(): bool
    {
        return empty($this->collection);
    }
}