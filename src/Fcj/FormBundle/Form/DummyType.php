<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cadet
 * Date: 4/10/13
 * Time: 9:38 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Fcj\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DummyType  extends AbstractType
{
    protected $thingToSay;

    public function __construct($thingToSay="Hey!")
    {
        $this->thingToSay = $thingToSay;
    }

    public function getName()
    {
        return 'fcj_dummy_type';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data' => $this->thingToSay,
            'data_class' => null
        ));
    }

    public function getParent()
    {
        return 'text';
    }
}