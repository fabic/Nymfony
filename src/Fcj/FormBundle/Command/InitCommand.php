<?php
/*
 */

namespace Fcj\FormBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Schema\SchemaException;

use Fcj\FormBundle\Entity\FormSpecScalar;

/**
 * Installs the tables required by the ACL system
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class InitCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('form:init')
            ->setDescription('Populate database with scalar FormSpecs.')
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
     * TODO/Idea: Auto-register form.type.* things ?
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /*
        $connection = $container->get('security.acl.dbal.connection');
        $schema = $container->get('security.acl.dbal.schema');

        try {
            $schema->addToSchema($connection->getSchemaManager()->createSchema());
        } catch (SchemaException $e) {
            $output->writeln("Aborting: " . $e->getMessage());

            return 1;
        }

        foreach ($schema->toSql($connection->getDatabasePlatform()) as $sql) {
            $connection->exec($sql);
        }
        $output->writeln('ACL tables have been initialized successfully.');
        */

        $em = $container->get('doctrine.orm.entity_manager');

        $serviceIds = $container->getServiceIds();
        $formTypes = array_filter($serviceIds, function($s) {
            return !substr_compare('form.type.', $s, 0, 10);
        });
        $formTypes = array_flip($formTypes);
        array_walk($formTypes, function(&$val, $key) use($container) {
            $name = substr($key, strrpos($key, '.')+1);
            $type = $container->get($key);
            $typeClass = get_class($type);
            $val = compact('name', 'typeClass');
        });

        //print_r($formTypes);

        foreach($formTypes AS $typeName => $t) {
            echo "Type '$typeName'\n";
            $scalar = new FormSpecScalar($t['name'], $typeName);
            $em->persist($scalar);
        }
        $em->flush();
        $output->writeln('Yeaaaah man!.');
    }
}
