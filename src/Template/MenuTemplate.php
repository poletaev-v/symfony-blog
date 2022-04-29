<?php

namespace App\Template;

use App\Utils\Menu\CategoryTree;

class MenuTemplate
{
    /**
     * @param CategoryTree[] $categories
     * @return void
     */
    public static function getContent(array $categories): string
    {
        $menu = self::getMenu($categories);
        return "
            <nav class='navbar navbar-expand-lg navbar-light bg-light'>
                <div class='container-fluid'>
                    <a class='navbar-brand' href=''/'>Navbar</a>
                    <button class='navbar-toggler' type='button'
                            data-bs-toggle='collapse'
                            data-bs-target='#main_nav'
                            aria-expanded='false' aria-label='Toggle navigation'>
                        <span class='navbar-toggler-icon'></span>
                    </button>
                    <div class='collapse navbar-collapse' id='main_nav'>
                        <ul class='navbar-nav'>
                            $menu
                        </ul>
                    </div>
                </div>
            </nav>";
    }

    private static function getMenu(array $categories): string
    {
        $content = '';
        foreach ($categories as $categoryTree) {
            $name = $categoryTree->getValue()->getName();

            if ($categoryTree->isEmpty()) {
                $content .= "
                    <li class='nav-item'>
                        <a class='nav-link dropdown-item' aria-current='page' href='#'>
                            $name
                        </a>
                    </li>";
            } else {
                $id = $categoryTree->getValue()->getId();
                $subMenu = self::getMenu($categoryTree->getChildren());

                $content .= "
                    <li class='nav-item dropdown'>
                        <a class='nav-link dropdown-toggle dropdown-item' href='#'
                           id='navbarDropdownMenuLink-$id'
                           data-bs-toggle='dropdown'>
                            $name
                        </a>
        
                        <ul class='submenu dropdown-menu'
                            aria-labelledby='navbarDropdownMenuLink-$id'>
                            $subMenu
                        </ul>
                    </li>";
            }
        }
        return $content;
    }
}