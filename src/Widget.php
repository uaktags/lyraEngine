<?php
namespace Lyra;

abstract class Widget extends Common implements \Lyra\Interfaces\Widget
{
    /**
     * {@inheritdoc}
     */
    protected $template;

    /**
     * {@inheritdoc}
     */
    protected $data;

    /**
     * {@inheritdoc}
     */
    public function setData(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Method imposed by the interface
     **/
    public function build(Array $parameters)
    {
    }

    public function getName()
    {

    }
}
