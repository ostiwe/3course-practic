<?php

namespace App\Command;

use App\Controller\CacheController;
use App\Entity\Auto;
use App\Entity\AutoModel;
use App\Entity\User;
use App\Entity\Workshop;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InitCommand extends Command
{
	private ContainerInterface $container;
	private ObjectManager $em;

	public function __construct(ContainerInterface $container)
	{
		parent::__construct();
		$this->container = $container;
		$this->em = $container->get('doctrine')->getManager();
	}

	protected function configure()
	{
		$this
			->setName('app:init')
			->setDescription('Add a short description for your command');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		if (!$io->confirm('Are you sure you want to continue? This action will delete all data in the selected database.')) {
			$io->text("Init canceled\n");
			return 0;
		}

		$cache = new CacheController();
		$cache->flushCache();

		$this->dropDbScheme($output);
		$this->createDbScheme($output);

		$this->createUsers();
		$this->em->flush();

		$this->createWorkshops();
		$this->em->flush();
		$this->createAutoModels();
		$this->em->flush();

		$count = $io->ask('Specify any number (minimum 100) to generate data', 100);

		$this->createAutos($count);
		$this->em->flush();

		$this->createAdmin();
		$this->em->flush();

		$io->success("App init done!\nYou can login with login/pass below:\n\nadmin/123456789");

		return 0;
	}

	private function dropDbScheme($output)
	{
		$dropSchema = $this->getApplication()->find('doctrine:schema:drop');
		$dropSchema->run(new ArrayInput(['--force' => true]), $output);
	}

	private function createDbScheme($output)
	{
		$createSchema = $this->getApplication()->find('doctrine:schema:create');
		$createSchema->run(new ArrayInput([['--env=dev']]), $output);
	}

	private function createAdmin()
	{
		$newUser = (new User())
			->setPassword(password_hash('123456789', PASSWORD_DEFAULT))
			->setLogin('admin')
			->setFirstName('Admin')
			->setLastName('Admin')
			->setMask(User::ADMIN);
		$this->em->persist($newUser);
	}

	private function createUsers()
	{
		$i = 0;
		$faker = Factory::create();

		do {
			$newUser = (new User())
				->setPassword(password_hash('123456789', PASSWORD_DEFAULT))
				->setLogin($faker->userName)
				->setFirstName($faker->firstName)
				->setLastName($faker->lastName)
				->setMask(User::DEFAULT_USER);
			$this->em->persist($newUser);
			$i++;
		} while ($i < 20);
	}

	private function createWorkshops()
	{
		$i = 1;
		$faker = Factory::create();
		do {
			/**  @var  User $user */
			$user = $this->em->getRepository(User::class)->find($i);
			$newWorkshop = (new Workshop())
				->setName($faker->userName)
				->setDirector($user);
			$this->em->persist($newWorkshop);
			$i++;
		} while ($i < 5);
	}

	private function createAutoModels()
	{
		$i = 0;
		$faker = Factory::create();
		do {
			$newModel = (new AutoModel())
				->setName($faker->userName);
			$this->em->persist($newModel);
			$i++;
		} while ($i < 15);
	}

	private function createAutos($count = 100)
	{
		if ((int)$count <= 100) $count = 100;

		$i = 0;
		$faker = Factory::create();
		do {
			$uuid = Uuid::uuid4();

			/** @var Workshop $workshop */
			$workshop = $this->em->getRepository(Workshop::class)->find(rand(1, 4));
			/** @var AutoModel $model */
			$model = $this->em->getRepository(AutoModel::class)->find(rand(1, 14));

			$neAuto = (new Auto())
				->setName($faker->userName)
				->setPower(rand(24, 250))
				->setSerialNumber($uuid->toString())
				->setCreatedAt(rand(time() - 1296000, time() + 1296000))
				->setWorkshop($workshop)
				->setModel($model);

			$this->em->persist($neAuto);
			$i++;
		} while ($i < $count);
	}

}
