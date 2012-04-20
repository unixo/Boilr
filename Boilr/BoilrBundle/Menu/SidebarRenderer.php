<?php

namespace Boilr\BoilrBundle\Menu;

use Knp\Menu\ItemInterface,
    Knp\Menu\Renderer\Renderer,
    Knp\Menu\Renderer\RendererInterface;

/**
 * Description of SidebarRenderer
 *
 * @author unixo
 */
class SidebarRenderer extends Renderer implements RendererInterface
{
    protected function getDefaultOptions()
    {
        return array(
            'depth' => null,
            'currentAsLink' => true,
            'currentClass' => 'active',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'header' => 'FERRU',
        );
    }

    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || 0 === $options['depth'] || !$item->getDisplayChildren()) {
            return '';
        }

        $attributes = $item->getAttributes();
        $attributes['class'] = 'nav nav-list';
        $html  = $this->format('<ul'.$this->renderHtmlAttributes($attributes).'>', 'ul', $item->getLevel());

        $headerAttr = array('class' => 'nav-header');
        //$html .= $this->format('<li'.$this->renderHtmlAttributes($headerAttr).'>', 'li', 1);
        $html .= '<li class="nav-header">'.$options['header'].'</li>';
        $html .= $this->renderChildren($item, $options);
        $html .= $this->format('</ul>', 'ul', $item->getLevel());

        return $html;
    }

    public function renderChildren(ItemInterface $item, array $options)
    {
        // render children with a depth - 1
        if (null !== $options['depth']) {
            $options['depth'] = $options['depth'] - 1;
        }

        $html = '';
        foreach ($item->getChildren() as $child) {
            $html .= $this->renderItem($child, $options);
        }

        return $html;
    }

    public function renderItem(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        // if we don't have access or this item is marked to not be shown
        if (!$item->isDisplayed()) {
            return '';
        }

        // explode the class string into an array of classes
        $class = ($item->getAttribute('class')) ? explode(' ', $item->getAttribute('class')) : array();

        if ($item->actsLikeFirst()) {
            $class[] = 'first';
        }
        if ($item->actsLikeLast()) {
            $class[] = 'last';
        }
        if ($item->isCurrent() || $item->isCurrentAncestor()) {
            $class[] = 'active';
            $class[] = 'active-trail';
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        // opening li tag
        $html = $this->format('<li'.$this->renderHtmlAttributes($attributes).'>', 'li', $item->getLevel());

        // render the text/link inside the li tag
        //$html .= $this->format($item->getUri() ? $item->renderLink() : $item->renderLabel(), 'link', $item->getLevel());
        $html .= $this->renderLink($item, $options);

        // renders the embedded ul if there are visible children
        if ($item->hasChildren() && 0 !== $options['depth'] && $item->getDisplayChildren()) {

            $childrenClass = ($item->getChildrenAttribute('class')) ? explode(' ', $item->getChildrenAttribute('class')) : array();
            $childrenClass[] = 'menu_level_'.$item->getLevel();

            $childrenAttributes = $item->getChildrenAttributes();
            $childrenAttributes['class'] = implode(' ', $childrenClass);

            $html .= $this->format('<ul'.$this->renderHtmlAttributes($childrenAttributes).'>', 'ul', $item->getLevel());
            $html .= $this->renderChildren($item, $options);
            $html .= $this->format('</ul>', 'ul', $item->getLevel());
        }

        // closing li tag
        $html .= $this->format('</li>', 'li', $item->getLevel());

        return $html;
    }

    public function renderLink(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        if ($item->isCurrent())
            return sprintf('<a href="%s">%s</a>', $item->getUri(), $item->getLabel());
        else {

        if ($item->getUri() && (!$item->isCurrent() || $options['currentAsLink'])) {
            $text = sprintf('<a href="%s"%s>%s</a>', $this->escape($item->getUri()), $this->renderHtmlAttributes($item->getLinkAttributes()), $this->escape($item->getLabel()));
        } else {
            $text = sprintf('<span%s>%s</span>', $this->renderHtmlAttributes($item->getLabelAttributes()), $this->escape($item->getLabel()));
        }

        return $this->format($text, 'link', $item->getLevel());
        }
    }

    protected function format($html, $type, $level)
    {
        switch ($type) {
        case 'ul':
        case 'link':
            $spacing = $level * 4;
            break;
        case 'li':
            $spacing = $level * 4 - 2;
            break;
        default:
            $spacing = 2;
        }

        return str_repeat(' ', $spacing).$html."\n";
    }
}
