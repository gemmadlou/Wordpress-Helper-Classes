<?php

namespace Helper;

class MenuReset
{
    private $postType;
    private $item;
    public $classes;

    public function __construct($classes, $item, $postType)
    {
        $this->classes = $classes;
        $this->item = $item;
        $this->postType = $postType;

        $this->addHomePageMenuItemClass();
        $this->removeBlogParentPageOnCustomPostTypes();
        $this->replaceWordpressClasses();
        $this->removeUnnecessaryWordpressClasses();
        $this->addGenericClass();
    }

    public function addPageTemplateAsMenuParent($pageTemplate, $postType, $cssClass = 'is-highlighted')
    {
        if (get_page_template_slug($this->item->object_id) === $pageTemplate
            && $this->postType === $postType) {
            $this->classes[] = $cssClass;
        }
    }

    private function addHomePageMenuItemClass() {
        if (get_option( 'page_on_front' ) === $this->item->object_id
            || get_option( 'page_on_front' ) === $this->item->object_id) {
            $this->classes[] = 'hide-on-desktop';
        }
    }

    private function removeBlogParentPageOnCustomPostTypes()
    {

        // Remove current_page_parent from classes if the current item is the blog page
        if ($this->postType != 'post' && $this->item->object_id == get_option('page_for_posts')) {
            $current_value = 'current_page_parent';
            $this->classes = array_filter($this->classes, function ($element) use ($current_value) {
                return $element != $current_value;
            });
        }
    }

    private function replaceWordpressClasses()
    {
        if (in_array('menu-item-has-children', $this->classes)) {
            $this->classes[] = 'c-nav__menu-item--parent';
            $this->classes[] = 'js-header__nav__item--parent';
        }

        if (in_array('current-menu-parent', $this->classes)) {
            $this->classes[] = 'c-nav__menu-item--parent';
        }

        if (in_array('current-menu-ancestor', $this->classes)) {
            $this->classes[] = 'c-nav__menu-item--currentpage-parent';
            $this->classes[] = 'js-header__nav-item--currentpage-parent';
        }

        if (in_array('current_page_item', $this->classes)) {
            $this->classes[] = 'c-nav__menu-item--current';
        }

        if (in_array('current_page_parent', $this->classes)) {
            $this->classes[] = 'is-highlighted';
        }
    }

    private function removeUnnecessaryWordpressClasses()
    {
        $classesToRemove = [
            'current-page-ancestor',
            'current_page_ancestor',
            'current-menu-ancestor',
            'current-page-parent',
            'current_page_parent',
            'current-menu-item',
            'current_page_item',
            'current-menu-parent',
            'menu-item-has-children',
            'menu-item',
            'menu-item-type-post_type',
            'menu-item-object-page',
            'page-item',
            'page_item',
        ];

        foreach ($classesToRemove as $class) {
            if (in_array($class, $this->classes)) {
                $key = array_search($class, $this->classes);
                unset($this->classes[$key]);
            }
        }
    }

    private function addGenericClass()
    {
        $this->classes[] = 'c-nav__menu-item';
    }
}
