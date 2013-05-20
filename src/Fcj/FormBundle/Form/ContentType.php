<?php

namespace Fcj\FormBundle\Form;

use Fcj\FormBundle\Entity\FormSpec;
use Fcj\FormBundle\Entity\FormSpecPart;
use Fcj\FormBundle\Entity\FormSpecFactory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

use JMS\DiExtraBundle\Annotation\FormType;
//    JMS\DiExtraBundle\Annotation\Inject,
//    JMS\DiExtraBundle\Annotation\InjectParams;


/**
 * Class ContentType : Sort of a generic form generator for a given FormSpec (or Content).
 *
 * Hence there's recursion in here so as to generate children contents.
 *
 * Note: ~~ This kind of works like a visitor pattern over a tree of form specification..?? ~~
 *
 * todo: strategy pattern for clients to extend form & view building ?
 * todo.. e.g. pre&post_build_Form&View(),
 * todo.. e.g. pre&post_partIteration(),
 *
 * @ FormType
 * @package Fcj\FormBundle\Form
 */
class ContentType extends AbstractType
{
    /**
     * @var FormSpec
     */
    protected $spec;

    /**
     * @var FormSpecPart|null
     */
    protected $part;

    /**
     * @var Content|null
     */
    protected $dataClass = null;

    /**
     * @param FormSpec $spec
     * @param FormSpecPart|null $part
     *
     * todo/? : Keep track of full traversal path ?
     */
    public function __construct(FormSpec $spec=null, FormSpecPart $part=null)
    {
        $this->spec = $spec;
        $this->part = $part;
    }

    /** Fixme?
     *
     * @param $className
     * @return void
     */
    public function setDataClass($className)
    {
        if( is_object($className) )
            $className = get_class( $className );
        $this->dataClass = $className;
    }

    /**
     * @inheritdoc
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formSpec = $this->spec ?: $options['formSpec'];

        $drops = $options['drops'];
        //if(! empty($drops))
        //    error_log (print_r(array_keys($drops),true));

        $data = isset($options['data']) ? $options['data'] : null;

        //$parentPart = isset($options['part']) ? $options['part'] : null;
        //assert(!$parentPart || $parentPart instanceOf FormSpecPart);
        //$parentPartName = $parentPart ? $parentPart->getName() : '';
        $parentPartName = $this->part ? $this->part->getName() : '';

        // todo: $formSpec->getIterator()->setRecursive(true) if embedded...?

        foreach($formSpec AS $part)
        {
            $to   = $part->getTo();
            assert($to!==NULL);

            $name = $part->getName();
            $name = str_replace('%', $parentPartName ?: 'xxx', $name);

            error_log(
                "»» " //__METHOD__
                . "From: " . $formSpec->getName() . " -- "
                . "to: " . $to->getName() . " -- "
                . "parentPart: $parentPartName --- name: $name"
            );

            // >> Merge options :
            $opts = array_merge(
                $to->getOptions(),
                $part->getOptions(),
                array(
                    'label'    => $part->getLabel(),
                    'required' => $part->isRequired(),
                    'virtual'  => $part->isEmbedded()
                )
            );

            // >> To "fork" or not to fork...
            if ($to->isBare())
                $type = $to->getName();
            else {
                $type = new self($to, $part);
                $opts['extra'] = $part->getExtra(); // fixme: see todo index.rst (type ext.)
                $opts['drops'] = array();
//                if ($data instanceOf Content) {
//                    $opts['data'] = $data
//                }
            }

            // >> Pass drops :
            foreach($part->getPass() AS $what => $def)
            {
                if (! isset($drops[$what])) {
                    error_log(__METHOD__ . "WARNING: Skipping unresolved drop '$what'.");
                    continue;
                }

                $drop = $drops[$what];

                $def = is_array($def) ? $def : array('as'=>'drop', 'key'=>$what);
                $as = $def['as'];
                $key = $def['key'];

                error_log("Pass '$what' as '$as' key '$key'");

                switch($as) {
                    case 'option':
                        $opts[$key] = $drop;
                        break;
                    case 'extra':
                        $opts['extra'][$key] = $drop;
                        break;
                    case 'drop':
                        $opts['drops'][$key] = $drop;
                    default:
                        error_log(
                            __METHOD__
                            . "WARNING: Unknown target (\$as=$as) for passing drop '$what'."
                        );
                }
            }

            // >> Add to Builder :
            if (! $part->isCollection()) {
                $builder->add($name, $type, $opts);
            }
            else {
                $builder->add($name, 'collection', array(
                    'type'    => $type,
                    'options' => $opts,
                    'data'    => array('Huh'=> array(), 'Hah'=> array()), // fixme: temp.
                    //'prototype'      => $part['prototype'],
                    //'prototype_name' => $part['prototype_name'],
                    //'allow_add'      => $part['allow_add'],
                    //'allow_delete'   => $part['allow_delete']
                ));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $specOpts = $this->spec ? $this->spec->getOptions() : array();
        $defaultOpts = array_merge(
            array(
                'data_class' => $this->dataClass, // fixme(temp): 'Fcj\FormBundle\Entity\Content' ?
                //'data_class' => 'Fcj\FormBundle\Entity\Content',
                'formSpec'   => $this->spec ?: null,
                'drops'      => array(),
                'extra'      => array()
            ),
            $specOpts);
        $resolver->setDefaults($defaultOpts);
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        $extends = $this->spec ? $this->spec->getExtends() : null;
        $extends = $extends ?: parent::getParent();
        return $extends;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        $name = $this->spec ? $this->spec->getName() : 'fcj_formbundle_contenttype';
        return $name;
    }
}
