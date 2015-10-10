<?php
namespace Lyra\Widget;

use Twig_Extension;
use Twig_Function_Method;

class WidgetTwigExtension extends Twig_Extension
{
    /**
     * @var WidgetRegistryInterface
     */
    protected $widgetRegistry;

    protected $environment;

    /**
     * @param WidgetRegistryInterface $widgetRegistry
     */
    public function __construct(\Lyra\WidgetRegistry $widgetRegistry, \Twig_Environment $environment)
    {
        $this->widgetRegistry = $widgetRegistry;
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gos_widget';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {

        return [
          new \Twig_SimpleFunction('widget',function($alias){
              echo $this->renderWidget($alias);
          }, [
            'is_safe' => ['html'],
            'need_environment' => true
          ])
        ];
    /*
        return [
            'widget' => new Twig_Function_Method($this, 'renderWidget', [
                'is_safe' => ['html'],
                'need_environment' => true
            ])
        ];
    */
    }

    /**
     * @param \Twig_Environment $environment
     * @param                   $alias
     * @param array $parameters
     *
     * @return string
     */
    public function renderWidget($alias, array $parameters = [])
    {
        $widget = $this->widgetRegistry->getWidget($alias);
        $widget->build($parameters);

        return $this->environment->render($widget->getTemplate());
    }
}
