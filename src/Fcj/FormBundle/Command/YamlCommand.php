<?php
/*
 */

namespace Fcj\FormBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
//use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\EntityManager;

use Fcj\FormBundle\Entity\FormSpec,
    Fcj\FormBundle\Entity\FormSpecPart,
    Fcj\FormBundle\Entity\FormSpecScalar;

/**
 *
 *
 * @author Fabien Cadet <cadet.fabien@gmail.com>
 */
class YamlCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('form:yaml:load')
            ->setDescription('Read a YAML file...')
            ->addArgument('file', null, 'The input YAML file')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command ...

<info>php %command.full_name%</info>

The name of the DBAL connection must be configured in your <info>app/config/security.yml</info> configuration file in the <info>security.acl.connection</info> variable.

<info>security:
    acl:
        connection: default</info>
EOF
            )
        ;
    }

    /**
     * @see Command::execute()
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('file');

        $container = $this->getContainer();

        $fcj = $container->get('fcj_form');

        $formSpecs = $fcj->loadFormSpecFromYamlFile( $path );

        print_r(array_keys($formSpecs));

        print_r(array_keys($fcj->getAllFormSpecs()));

        foreach($fcj->getAllFormSpecs() AS $fsName => $fs)
        {
            echo "FormSpec '$fsName' :\n";
            foreach($fs AS $partName => $part)
            {
                $to = $part->getTo();
                echo " » Part '$partName' to FormSpec '{$to->getName()}'\n";
            }
            echo "\n";
        }

        $fcj->bakeFormSpecs($formSpecs);

        //$em->flush();
        $output->writeln('Yeaaaah man!');
    }



}
