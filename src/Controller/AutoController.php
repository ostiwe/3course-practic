<?php


namespace App\Controller;


use App\Entity\Auto;
use App\Entity\AutoModel;
use App\Entity\Workshop;
use App\ErrorHelper;
use App\ParamsChecker;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AutoController extends AbstractController
{
	/**
	 * @Route("/auto",methods={"POST"})
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function create(Request $request)
	{
		$body = json_decode($request->getContent(), true);

		$errors = ParamsChecker::check([
			'model_id', 'power', 'workshop_id',
		], $body);

		if (count($errors) !== 0) return $this->json(ErrorHelper::requestWrongParams($errors));

		$model = $this->getDoctrine()->getRepository(AutoModel::class)->find($body['model_id']);
		$workshop = $this->getDoctrine()->getRepository(Workshop::class)->find($body['workshop_id']);

		if (!$model) return $this->json(ErrorHelper::modelNotFound());
		if (!$workshop) return $this->json(ErrorHelper::workshopNotFound());

		if (key_exists('created_at', $body) && $body['created_at'] !== 0) {
			$createdAt = $body['created_at'];
		} else $createdAt = time();

		$uuid = Uuid::uuid4();

		$newAuto = (new Auto())
			->setCreatedAt($createdAt)
			->setSerialNumber($uuid->toString())
			->setWorkshop($workshop)
			->setPower((int)$body['power'])
			->setModel($model);

		try {
			$this->getDoctrine()->getManager()->persist($newAuto);
			$this->getDoctrine()->getManager()->flush();
			return $this->json(['success' => true, 'auto_id' => $newAuto->getId()]);
		} catch (\Exception $exception) {
			return $this->json(['error' => true, 'message' => 'try again later', '_' => $exception->getMessage()]);
		}
	}

	/**
	 * @Route("/auto/{autoID}",methods={"DELETE"})
	 * @param Request $request
	 * @param         $autoID
	 *
	 * @return JsonResponse
	 */
	public function remove(Request $request, $autoID)
	{
		if ((int)$autoID === 0) return $this->json(ErrorHelper::invalidRequest());

		$auto = $this->getDoctrine()->getRepository(Auto::class)->find($autoID);

		if (!$auto) return $this->json(ErrorHelper::autoNotFound());

		try {
			$this->getDoctrine()->getManager()->remove($auto);
			$this->getDoctrine()->getManager()->flush();

			if ($request->query->has('p')) {
				$cache = new CacheController();
				$cache->removeItem("autos.auto_{$request->query->get('p')}");

				$this->painate($request->query->get('p'));
			}

			return $this->json(['success' => true]);
		} catch (\Exception $exception) {
			return $this->json(['error' => true, 'message' => 'try again later']);
		}
	}

	/**
	 * @Route("/autos",methods={"GET"})
	 */
	public function index()
	{
		$autoList = $this->getDoctrine()->getRepository(Auto::class)->findAll();

		$autos = array_map(function (Auto $auto) { return $auto->export(); }, $autoList);

		return $this->json(['success' => true, 'items' => $autos]);
	}

	/**
	 * @Route("/autos/{page}",methods={"GET"})
	 * @param $page
	 *
	 * @return JsonResponse
	 */
	public function painate($page)
	{
		$cache = new CacheController();

		if ((int)$page <= 0) $page = 1;

		if ($cache->inCache("autos.auto_$page")) {
			return $this->json($cache->getItemFromCache("autos.auto_$page"));
		}

		$items = $this->getDoctrine()->getRepository(Auto::class)->paginate($page, 50);


		$statsResponse = [];
		foreach ($items['items'] as $auto) {
			unset($auto['serial_number']);
			$date = date('d.m.Y', $auto['created_at']);
			$auto['_page'] = (int)$page;
			$statsResponse[$date][] = $auto;
		}

		$response = ['success' => true];

		$response = array_merge($response, ['items' => $statsResponse]);

		$cache->setCache("autos.auto_$page", $response);

		return $this->json($response);

	}


	/** @Route("/auto/gen") */
	public function gen()
	{
		$i = 0;
		$faker = Factory::create();
		do {
			$uuid = Uuid::uuid4();

			$workshop = $this->getDoctrine()->getRepository(Workshop::class)->find(rand(1, 8));
			$model = $this->getDoctrine()->getRepository(AutoModel::class)->find(rand(1, 5));

			$auto = (new Auto())
				->setName($faker->userName)
				->setPower(rand(24, 250))
				->setSerialNumber($uuid->toString())
				->setModel($model)
				->setWorkshop($workshop)
				->setCreatedAt(rand(time() - 1296000, time() + 1296000));

			$this->getDoctrine()->getManager()->persist($auto);
			$i++;
		} while ($i < 4500);

		$this->getDoctrine()->getManager()->flush();

		return $this->json(['success' => true]);
	}
}
