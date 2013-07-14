<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fabi
 * Date: 7/14/13
 * Time: 2:00 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Fcj\MouvsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** Base command class for MouvsBundle commands...
 *
 * Class BaseMouvsCommand
 * @package Fcj\MouvsBundle\Command
 */
abstract class BaseMouvsCommand extends ContainerAwareCommand
{

    /** Helper for obtaining the MouvsBundle service thing.
     *
     * @return \Fcj\MouvsBundle\MouvsService
     */
    public function mouvs()
    {
        return $this->getContainer()->get('mouvs');
    }
}