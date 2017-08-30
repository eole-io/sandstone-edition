<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use App\Event\HelloEvent;

class HelloCommand extends Command
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        parent::__construct('sandstone:hello');

        $this->dispatcher = $dispatcher;
    }

    /**
     * {@InheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Sandstone test command. Dispatch an event that should be forwarded.')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name to greet.', 'world')
        ;
    }

    /**
     * {@InheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $output->writeln("Hello $name");

        $event = new HelloEvent($name);

        $output->writeln("Dispatching event");
        $this->dispatcher->dispatch(HelloEvent::HELLO, $event);
    }
}
