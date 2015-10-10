<?php
namespace Lyra;

use Lyra\Interfaces\Widget;

class WidgetRegistry implements \Lyra\Interfaces\WidgetRegistry
{
    /**
     * {@inheritdoc}
     */
    protected $widgets;

    public function __construct()
    {
        $this->widgets = [];
    }

    /**
     * {@inheritdoc}
     */
    public function addWidget(\Lyra\Interfaces\Widget $widget)
    {
        return $this->widgets[$widget->getName()] = $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidget($alias)
    {
        if (!isset($this->widgets[$alias])) {
            throw new ServiceNotFoundException(sprintf('Widget %s is actually not load into the WidgetRegistry'),
                $alias);
        }

        return $this->widgets[$alias];
    }
}
