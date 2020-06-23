<?php


namespace App\Controller;


use App\Entity\AutoModel;
use App\ErrorHelper;
use App\ParamsChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AutoModelsController extends AbstractController
{
	/**
	 * @Route("/auto/models",methods={"POST"})
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function create(Request $request)
	{
		$body = json_decode($request->getContent(), true);

		$errors = ParamsChecker::check(['name'], $body);

		if (count($errors) !== 0) return $this->json(ErrorHelper::requestWrongParams($errors));

		$newModel = (new AutoModel())
			->setName($body['name']);

		try {
			$this->getDoctrine()->getManager()->persist($newModel);
			$this->getDoctrine()->getManager()->flush();
			return $this->json(['success' => true, 'model_id' => $newModel->getId()]);
		} catch (\Exception $exception) {
			return $this->json(['error' => true, 'message' => 'try again later']);
		}
	}

	/**
	 * @Route("/auto/models/{modelID}",methods={"DELETE"})
	 *
	 * @param $modelID
	 *
	 * @return JsonResponse
	 */
	public function remove($modelID)
	{
		if ((int)$modelID === 0) return $this->json(ErrorHelper::invalidRequest());

		$model = $this->getDoctrine()->getRepository(AutoModel::class)->findAll($modelID);

		if (!$model) return $this->json(ErrorHelper::modelNotFound());

		try {
			$this->getDoctrine()->getManager()->remove($model);
			$this->getDoctrine()->getManager()->flush();
		} catch (\Exception $exception) {
			return $this->json(['error' => true, 'message' => 'try again later']);
		}

		return $this->json(['success' => true]);

	}

	/** @Route("/auto/models",methods={"GET"}) */
	public function index()
	{
		$autoList = $this->getDoctrine()->getRepository(AutoModel::class)->findAll();

		$autos = array_map(function (AutoModel $auto) { return $auto->export(); }, $autoList);

		return $this->json(['success' => true, 'items' => $autos]);
	}
}
