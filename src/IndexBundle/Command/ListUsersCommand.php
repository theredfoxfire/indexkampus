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

By default the command only displays the 50 most recent users. Set the number of
results to display with the <comment>--max-results</comment> option:

  <info>php %command.full_name%</info> <comment>--max-results=2000</comment>

In addition to displaying the user list, you can also send this information to
the email address specified in the <comment>--send-to</comment> option:

  <info>php %command.full_name%</info> <comment>--send-to=fabien@symfony.com</comment>

HELP
			)
		->addOption('--max-results', null, InputOption::VALUE_OPTIONAL, 'Limits the number of users listed', 50)
		->addOption('send-to', null, InputOption::VALUE_OPTIONAL, 'If set, the result is sent to the given email address')
		;
	}

	/**
	* This method is executed before the execute() method. It's main purpose
	* is to intialize the variables used in the best of the command methods
	*/
	protected function intialize(InputInterface $input, OutputInterface $output)
	{
		$this->em = $this->getContainer()->get('doctrine')->getManager();
	}

	/**
	* This method is executed after intialize().
	*/
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$maxResults = $input->getOption('max-results');
		//Use ->findBy() instead of ->findAll() to allow result sorting and limiting
		$users = $this->em->getRepository('IndexBundle:User')->findBy(array(), array('id' => DESC), $maxResults);

		//Doctrine query returns an array of objects and we need an array of plain arrays
		$usersAsPlainArrays = array_map(function ($user) {
			return array($user->getId(), $user->getUsername(), $user->getEmail(), implode(', ', $user->getRoles()));
		}, $users);

		$bufferedOutput = new BufferedOutput();

		$table = new Table($bufferedOutput);
		$table->setHeaders(array('ID', 'Username', 'Email', 'Roles'))
			->setRows($usersAsPlainArrays);
		$table->render();

		$tableContents = $bufferedOutput->fetch();

		if (null !== $email = $input->getOption('send-to')) {
			$this->sendReport($tableContents, $email);
		}

		$output->writeln($tableContents);
	}

	/**
	* Spends the given $contents to the $recipient email address.
	* 
	* @param string $contents
	* @param string $recipient
	*/
	private function sendReport($contents, $recipient)
	{
		$mailer = $this->getContainer()->get('mailer');

		$message = $mailer->createMessage()
			->setSubject(sprintf('app:list-users report (%s)', date('Y-m-d H:i:s')))
			->setForm($this->getContainer()->getParameter('app.notifications.email_sender'))
			->setTo($recipient)
			->setBody($contents, 'text/plain')
			;

		$mailer->send($message);
	}
}