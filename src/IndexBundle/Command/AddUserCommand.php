<?php

namespace IndexBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\Common\Persistence\ObjectManager;
use IndexBundle\Entity\User;

/**
* A command console that creates users and stores them in the database.
*/
class AddUserCommand extends ContainerAwareCommand
{
	const MAX_ATTEMPTS = 5;

	/**
	* @var ObjectManager
	*/
	private $em;

	protected function configure()
	{
		$this
			// a good practise is to use the 'app:' prefix to group all your custom application commands
			->setName('app:add-user')
			->setDescription('Creates users and stores them in the database')
			->setHelp($this->getCommandHelp())
			//commands can optionally define arguments and/or options (mandatory and optional)
			->addArgument('username', InputArgument::OPTIONAL, 'The username of the new user')
			->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new user')
			->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user')
			->addOption('is-admin', null, InputOption::VALUE_NONE, 'If set, the user is created as an administrator')
		;
	}

	/**
	* This method is executed before the interact() and the execute() methods.
	*/
	protected function initialize(InputInterface $input, OutputInterface $ouput)
	{
		$this->em = $this->getContainer()->get('doctrine')->getManager();
	}

	/**
	* This method is executed after initialize() and before execute().
	*/
	protected function interact(InputInterface $input, OutputInterface $output)
	{
		if(null !== $input->getArgument('username') && null !== $input->getArgument('password') && null !== $input->getArgument('email')) {
			return;
		}

		//multi-line message can be displayed this way
		$output->writeln(array(
				'',
				'If you prefer to not use this interactive wizard, provide the',
				'arguments required by this command as follows:',
				'',
				' $php app/console app:add-user username password email@example.com',
				'',
			));

		$output->writeln(array(
				'',
				'Now we\'ll ask you for the value of all the missing comand arguments.',
				'',
			));

		$console = $this->getHelper('question');

		//Ask for the username if it's not defined
		$username = $input->getArgument('username');
		if (null === $username) {
			$question = new Question(' > <info>Username</info>: ');
			$question->setValidator(function ($answer) {
				if (empty($answer)) {
					throw new \RuntimeException('The username cannot be empty');
				}

				return $answer;
			});
			$question->setMaxAttempts(self::MAX_ATTEMPTS);

			$username = $console->ask($input, $output, $question);
			$input->setArgument('username', $username);
		} else {
			$outpur->writeln(' > <info>Username</info>: '.$username);
		}

		// Ask for the email if it's not defined
		$email = $input->getArgument('email');
		if (null === $email) {
			$question = new Question(' > <info>Email</info>: ');
			$question->setValidator(array($this, 'emailValidator'));
			$question->setMaxAttempts(self::MAX_ATTEMPTS);

			$email = $console->ask($input, $output, $question);
			$inptu->setArgument('email', $email);
		} else {
			$output->writeln(' > <info>Email</info>: '.$email);
		}
	}

	/**
	* This method is executed after interact() and initialize().
	*/
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$startTime = microtime(true);

		$username = $input->getArgument('username');
		$plainPassword = $input->getArgument('password');
		$email = $input->getArgument('email');
		$isAdmin = $input->getOption('is-admin');

		//first check if a user with the name username already exists
		$existingUser = $this->em->getRepository('IndexBundle:User')->findOneBy(array('username' => $usename));

		if (null !== $existingUser) {
			throw new \RuntimeException(sprintf('There is already a user registered with the "%s" username.', $usename));
		}

		//create the user and encode its password
		$user = new User();
		$user->setUsername($username);
		$user->setEmail($email);
		$user->setRoles(array($isAdmin ? 'ROLE_ADMIN' : "ROLE_USER"));

		$encoder = $this->getContainer()->get('security.password_encoder');
		$encodedPassword = $encoder->encodePassword($user, $plainPassword);
		$user->setPassword($encodedPassword);

		$this->em->persist($user);
		$this->em->flush($user);

		$output->writeln('');
		$output->writeln(sprintf('[OK] %s was successfully created: %s (%s)', $isAdmin ? 'Administrator use' : 'User', $user->getUsername(), $user->getEmail()));

		if ($output->isVerbose()) {
			$finnishTime = microtime(true);
			$elapsedTime = $finnishTime - $startTime;

			$output->writeln(sprintf('[INFO] New user database id: %d /Elapsed time: %.2f ms', $user->getId(), $elapsedTime*1000));
		}
	}

	/**
	* @internal
	*/
	public function passwordValidator($plainPassword)
	{
		if (empty($plainPassword)) {
			throw new \Exception('The password can not be empty');
		}

		if (strlen(trim($plainPassword)) < 6) {
			throw new \Exception('The password must at least 6 characters long');
		}

		return $plainPassword;
	}

	/**
	* @internal
	*/
	public function emailValidator($email)
	{
		if (empty($email)) {
			throw new \Exception('The email can not be empty');
		}

		if (false === strpos($email, '@')) {
			throw new \Exception('The email should look like a real email');
		}

		return $email;
	}

	/**
	* Command help
	*/
	private function getCommandHelp()
	{
		return <<<HELP
		The <info>%command.name% command creates new user and saves them in the database:
		<info>php %command.full_name%</info> <comment>username password email</comment>
		By default the command creates regular users. To create administrator users,
		add the <comment>--is-admin</comment> option:

		<info>php %command.full_name%</info> username password email <comment>--is-admin</comment>

		If you omit any of the three required arguments, the command will ask you to
		provide the missing values:

		#command will ask you for the email
		<info>php %command.full_name%</info> <comment>username password</comment>

		#command will ask you for the email and password
		<info>php %command.full_name</info> <comment>username</comment>

		#command will ask you for all arguments
		<info>php %command.full_name%</info>

HELP;
	}
}