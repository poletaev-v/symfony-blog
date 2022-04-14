<?php

namespace App\Template;

use App\Utils\Menu\CategoryTree;

class MenuTemplate
{
    /**
     * @param CategoryTree[] $categories
     * @return void
     */
    public static function render(array $categories): string
    {
        ?>

        <?php
        ob_start();
        ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">Navbar</a>
                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#main_nav"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="main_nav">
                    <ul class="navbar-nav">
                        <?php foreach ($categories as $category) {
                            self::renderMenu($category);
                        } ?>
                    </ul>
                </div>
            </div>
        </nav>

        <?php
        $content = (string)ob_get_contents();
        ob_end_clean();
        return $content;
        ?>

        <?php
    }

    private static function renderMenu(CategoryTree $categoryTree): void
    {
        ?>
        <?php if ($categoryTree->isEmpty()) { ?>
            <li class="nav-item">
                <a class="nav-link dropdown-item" aria-current="page" href="#">
                    <?= $categoryTree->getValue()->getName() ?>
                </a>
            </li>
        <?php } else { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle dropdown-item" href="#"
                   id="navbarDropdownMenuLink-<?= $categoryTree->getValue()->getId() ?>"
                   data-bs-toggle="dropdown">
                    <?= $categoryTree->getValue()->getName() ?>
                </a>

                <ul class="submenu dropdown-menu"
                    aria-labelledby="navbarDropdownMenuLink-<?= $categoryTree->getValue()->getId() ?>">
                    <?php foreach ($categoryTree->getChildren() as $subCategory) { ?>
                        <?php self::renderMenu($subCategory); ?>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php
    }
}