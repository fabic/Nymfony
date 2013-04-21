<?php
/**
 */

namespace Fcj\FormBundle;

use Fcj\FormBundle\Entity\Content;
use Fcj\FormBundle\Entity\FormSpec;
use Fcj\FormBundle\Entity\FormSpecPart;
//use Fcj\FormBundle\Entity\FormSpecScalar;
use Fcj\FormBundle\Form\ContentType;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Yaml\Yaml;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormRegistry;

use Symfony\Component\Form\ResolvedFormType;
use Symfony\Component\Form\ResolvedFormTypeFactory;

/**
 * FormFactory
 *
 * @package Fcj\FormBundle
 *
 * todo: clean up: Get rid of $formRegistry (unused).
 */
class FormSpecFactory
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $fsRepo;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var FormRegistry
     */
    protected $formRegistry;

    /**
     * @var ArrayCollection<FormSpec> indexed by FormSpec::name.
     */
    protected $registry;

    /** See Resources/config/services.yml for DI definition.
     *
     * @param FormFactory $ff
     * @param FormRegistry $fr
     * @param EntityManager $em
     */
    public function __construct(FormFactory $ff, FormRegistry $fr,
                                EntityManager $em)
    {
        $this->em = $em;
        $this->fsRepo = $em->getRepository('FcjFormBundle:FormSpec');
        $this->formFactory  = $ff;
        $this->formRegistry = $fr;
        $this->registry = new ArrayCollection();
        //
        //$rft = new ResolvedFormType()
        //$this->formRegistry->addType();
    }

    /**
     * @param $specName
     * @return bool True if a FormSpec named $specName exists in the registry.
     *
     * todo: s/exists/registryKnows
     */
    public function exists($specName)
    {
        $found = $this->registry->containsKey($specName);
        //error_log(print_r($specName,true) . ": Found=" . ($found ? "yes" : "no"));
        return $found;
    }

    /** Get a FormSpec by looking up the registry, or storage backend load it,
     *  or eventually create a new instance of it if $registerUnresolved is true.
     *
     * @param string $name
     * @param bool $registerUnresolved
     * @return FormSpec|null
     *
     * todo: Ability to have $name an integer for looking up by form spec. db ID ?
     */
    public function getFormSpec($name, $registerUnresolved=false)
    {
        $name = (string) $name;
        // Registry lookup :
        if( $this->exists($name) )
            return $this->registry->get($name); // todo: registryGet ?
        // Storage (DB) lookup happens here :
        $formSpec = $this->fsRepo->findByName($name);
        // New & add to registry if requested :
        if (!$formSpec && $registerUnresolved) {
            $formSpec = new FormSpec($name);
            $this->registry->set($name, $formSpec);
        }
        return $formSpec;
    }

    /** Ensure $spec is a FormSpec, or load it from storage backend if not,
     *  hence $spec would be the name of the FormSpec to lookup.
     *
     * @param string|FormSpec $spec
     * @return FormSpec|null
     */
    public function formSpec($spec)
    {
        if(! $spec instanceOf FormSpec)
            $spec = $this->getFormSpec($spec);
        return $spec;
    }


    /**
     * @return Array<FormSpec>
     */
    public function getAllFormSpecs()
    {
        return $this->registry->toArray();
    }

    /**
     * @param FormSpec|string $formSpec
     * @param null $data
     * @param array $options
     * @param array $drops
     * @return \Symfony\Component\Form\Form
     */
    public function createForm($formSpec, $data=null,
                               Array $options=array(), Array $drops=array())
    {
        $formSpec = $this->formSpec($formSpec);
        // Content :
        $ct = new ContentType($formSpec);

        if ($data instanceOf Content) { // todo: think about this...
            //$data->setFormSpec($formSpec);
            $ct->setDataClass($data);
        }
        // Drops :
        $options['drops'] = $drops;
        // Form :
        $form = $this->formFactory->create($ct, $data, $options);
        return $form;
    }

    /**
     * @param Content $content
     * @param array $options
     * @param array $drops
     * @return Form
     */
    public function createFormForContent(Content $content, Array $options=array(), Array $drops=array())
    {
        $formSpec = $content->getFormSpec();
        return $this->createForm($formSpec, $content, $options, $drops);
    }

    /**
     * @param $name
     * @param string $specName
     * @param null $data
     * @param array $options
     * @param array $drops
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function createNamedBuilder($name, $specName, $data=null,
                                       Array $options=array(), Array $drops=array())
    {
        $spec = $this->getFormSpec($specName);
        $ct = new ContentType($spec);
        $options['drops'] = $drops;
        $builder = $this->formFactory->createNamedBuilder($name, $ct, $data, $options);
        return $builder;
    }

    /**
     * @param $fileName
     * @param bool $register
     * @return Array<FormSpec>
     */
    public function loadFormSpecFromYamlFile($fileName, $register=true)
    {
        $yaml = Yaml::parse($fileName);

        $formSpecs = Array();

        // Abbr. fsXXX is for formSpecXXX.
        foreach($yaml AS $fsName => $fsDef)
        {
            $fsDef['name'] = isset($fsDef['name']) ? $fsDef['name'] : $fsName;

            $formSpec = $this->formSpecFromYamlDefinition($fsDef);

            $formSpecs[$formSpec->getName()] = $formSpec;


            // TODO ??
            if ($register)
                    ;

            // todo: load from registry/db so as to update/merge things ?
            // fixme: check if exists! then what to do? update def.? merge?
            //$this->registry->set($formSpec->getName(), $formSpec);

            //print_r($formSpec);

            //$em->persist($formSpec);
            //$em->flush($formSpec);
        }

        return $formSpecs;
    }

    /**
     * @param array $definition
     * @return FormSpec
     */
    protected function formSpecFromYamlDefinition(Array $definition, $replace=false)
    {
        $formSpec = $this->getFormSpec($definition['name'], true); //new FormSpec($definition['name']);

        // >>> PROCESS this form parameters :
        $extends = $embedded = null;
        $extra = $options = array();
        foreach(array('extends', 'embedded', 'extra', 'options') AS $key)
            if( isset($definition[$key]) )
                $$key = $definition[$key];

        $formSpec->setExtends ( $extends );
        $formSpec->setEmbedded( $embedded);
        $formSpec->setExtra   ( $extra   );
        $formSpec->setOptions ( $options );

        // >>> PROCESS parts :
        if( isset($definition['parts']) ) {
            $parts = $definition['parts'];
            foreach($parts as $partName => $p) {
                $to   = $this->getFormSpec($p['type'], true);
                $part = new FormSpecPart($formSpec, $to, $partName);

                if (isset($p['label']))      $part->setLabel     ($p['label']);
                if (isset($p['required']))   $part->setRequired  ($p['required']);
                if (isset($p['collection'])) $part->setCollection($p['collection']);
                if (isset($p['embedded']))   $part->setEmbedded  ($p['embedded']);
                if (isset($p['extra']))      $part->setExtra     ($p['extra']);
                if (isset($p['options']))    $part->setOptions   ($p['options']);
                if (isset($p['pass']))       $part->setPass      ($p['pass']);

                $formSpec->addPart($part);
            }
        }

        // >>> PROCESS presentation :
        // todo...

        return $formSpec;
    }

    /**
     * TODO: Order parts for deciding how to persist-flush specs!!
     */
    public function bakeFormSpecs()
    {
        foreach($this->registry AS $fsName => $fs)
        {
            if(! $this->em->contains($fs)) {
                $this->em->persist($fs);
                $this->em->flush($fs);
                error_log("Persisting FormSpec '$fsName'.");
            }

            error_log("Flush! FormSpec '$fsName'.");
            $this->em->flush($fs);
        }
        return ;
    }
}