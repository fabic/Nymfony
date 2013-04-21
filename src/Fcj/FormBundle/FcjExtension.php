<?php
/**
 */
namespace Fcj\FormBundle;

use Symfony\Component\Form\AbstractExtension;

/**
 * Class FcjExtension
 * @package Fcj\FormBundle
 * @see http://localhost:8000/nymfony/doc/class-Symfony.Component.Form.AbstractExtension.html
 */
class FcjExtension extends AbstractExtension
{

    /**
     * @var FormSpecFactory
     */
    protected $factory;

    /**
     * @param FormSpecFactory $factory
     */
    public function __construct(FormSpecFactory $factory)
    {
        error_log("HHHHHHHHHHHHHHHHHHHHHHHHH");
        $this->factory = $factory;
    }

    protected function loadTypes()
    {
        error_log("OOOOOOOOOOOOO");

        return array(
            new Fcj\FormBundle\Form\DummyType("YEAH! we're registered from " . __CLASS__)
        );
    }

}