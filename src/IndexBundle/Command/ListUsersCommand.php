<?php

namespace IndexBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

/**
* A command console that lists all the existing users.
* To use this command enter into project directory and enter following command:
* $ php app/console app:list-users
*/
class ListUsersCommand extends ContainerAwareCommand
{
	/**
	* @var ObjectManager
	*/
	private $em;

	protected function configure()
	{
		$this
		// a good practise is to use the app: prefix to group all your custom appliaction commands
		->setName('app:list-users')
		->setDescription('Lists all the existing users')
		->setHelp(<<<HELP 
				The <info>%command.name%</info> command lists all the users registered in the application:
				<info>php %command.full_name%</info>

				By default the command only displays the 50 most recent users. set the number of
				results to display with the <comment>--max-results</comment> option:
			)
	}
}