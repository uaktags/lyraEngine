<?php
namespace Module\Login\Widgets;


class LoginForm extends \Lyra\Widget
{
    /**
     * Use the constructor to inject your dependencies
     **/
    public function __construct()
    {
    }

    /**
     * Method imposed by the interface
     **/
    public function build(Array $parameters)
    {

        //Set the template to be render
        $this->setTemplate('login_form.widget.twig');

        //Data passed to the view
        $this->setData(array(
            'name' => 'I\'am a Login Form Widget!'
        ));
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        /**
         * Define the name of the widget to retrieve it in the twig template
         **/
        return 'login_form';
    }
}