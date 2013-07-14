<?php
/** File: src/Fcj/MouvsBundle/Command/UpdateCommand.php
 */
namespace Fcj\MouvsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Fcj\MouvsBundle\Entity\FileSource;
use Doctrine\Common\Collections\Collection;

/**
 *
 *
 * @author Fabien Cadet <cadet.fabien@gmail.com>
 */
class UpdateCommand extends BaseMouvsCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('mouvs:sync')
            ->setDescription('Sync database & on-disk files...')
            //->addArgument('file', null, 'The input YAML file')
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
        $mouvs = $this->mouvs();

        $sources = $mouvs->fileSources();

        /** @var FileSource $fs */
        foreach($sources AS $fs)
        {
            $output->writeln("=== Source {$fs->getId()}: {$fs->getPath()}");
            $files = $mouvs->sync($fs);
            //print_r ($files->getKeys());
        }

        $output->writeln('Yeaaaah man!');
    }

}
