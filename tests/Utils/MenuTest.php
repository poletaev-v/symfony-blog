<?php

namespace App\Tests\Utils;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Utils\Menu\CategoryTree;
use App\Utils\Menu\Menu;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Cache\CacheInterface;

class MenuTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel([
            'debug' => false,
        ]);
    }

    public function testLoad(): void
    {
        $container = static::getContainer();
        $stubCategoryRepo = $this->createMock(CategoryRepository::class);
        $rootCategory = (new Category())
            ->setName('Test category 1')
            ->setId(1);
        $childCategory = (new Category())
            ->setName('Test category 2')
            ->setId(2)
            ->setParentCategory($rootCategory);
        $stubCategoryRepo->method('findAll')
            ->willReturn([$rootCategory, $childCategory]);

        $cache = $container->get(CacheInterface::class);
        $menu = new Menu($stubCategoryRepo, $cache);
        $menu->load();

        $categoryTree = new CategoryTree($rootCategory);
        $categoryTree->getCollection()
            ->push(new CategoryTree($childCategory));

        $this->assertEquals([$rootCategory->getId() => $categoryTree], $menu->categoryList);
    }
}