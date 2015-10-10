<?php
namespace Lyra\Interfaces;

interface WidgetRegistry
{
    /**
     * @param WidgetInterface $widget
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @return void
     */
    public function addWidget(\Lyra\Interfaces\Widget $widget);

    /**
     * @param $alias
     *
     * @return mixed
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function getWidget($alias);
}
