<?php

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Application as ConsoleApplication;
use Application as SilexApplication;
use App\Command\HelloCommand;

class Console extends ConsoleApplication
{
    /**
     * @var SilexApplication
     */
    private $silexApplication;

    /**
     * Console application constructor.
     *
     * @param SilexApplication $silexApplication The Sandstone RestApi stack
     *                                           (to have push event from console commands working).
     */
    public function __construct(SilexApplication $silexApplication)
    {
        parent::__construct('My Sandstone application');

        $this->silexApplication = $silexApplication;
        $this->silexApplication->boot();

        $this->registerDoctrineCommands();
        $this->registerUserCommands();
    }

    /**
     * User custom commands.
     */
    private function registerUserCommands()
    {
        $this->add(new HelloCommand($this->silexApplication['dispatcher']));
    }

    private function registerDoctrineCommands()
    {
        $em = $this->silexApplication['orm.em'];

        // Register Doctrine ORM commands
        $helperSet = new HelperSet(array(
            'db' => new ConnectionHelper($em->getConnection()),
            'em' => new EntityManagerHelper($em)
        ));

        $this->setHelperSet($helperSet);
        ConsoleRunner::addCommands($this);
    }
}
