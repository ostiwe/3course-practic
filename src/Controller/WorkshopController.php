<?php


namespace App\Controller;


use App\Entity\User;
use App\Entity\Workshop;
use App\ErrorHelper;
use App\ParamsChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkshopController extends AbstractController
{
	/**
	 * @Route("/workshops",methods={"POST"})
	 * @param Request            $request
	 * @param ValidatorInterface $validator
	 *
	 * @return JsonResponse
	 */
	public function create(Request $request, ValidatorInterface $validator)
	{
		$body = json_decode($request->getContent(), true);

		if (!isset($body['director']) || (int)$body['director'] <= 0)
			return $this->json(ErrorHelper::invalidRequest());

		$director = $this->getDoctrine()->getRepository(User::class)->find($body['director']);

		if (!$director) return $this->json(ErrorHelper::userNotFound());

		$newWorkshop = (new Workshop())
			->setName($body['name'] ?? '')
			->setDirector($director);

		$errors = $validator->validate($newWorkshop);

		if ($errors->count() > 0) {
			$errorsList = [];
			/** @var ConstraintViolation $error */
			foreach ($errors as $error) {
				$errorsList[$error->getPropertyPath()][] = $error->getMessage();
			}
			return $this->json(ErrorHelper::requestWrongParams($errorsList));
		}
		try {
			$this->getDoctrine()->getManager()->persist($newWorkshop);
			$this->getDoctrine()->getManager()->flush();
			return $this->json(['success' => true, 'workshop_id' => $newWorkshop->getId()]);

		} catch (\Exception $exception) {
			return $this->json(['error' => true, 'message' => 'one user cannot manage multiple workshops']);
		}

	}

	/**
	 * @Route("/workshop/{workshopID}",methods={"PUT"})
	 * @param Request            $request
	 * @param ValidatorInterface $validator
	 * @param                    $workshopID
	 *
	 * @return JsonResponse
	 */
	public function change(Request $request, ValidatorInterface $validator, $workshopID)
	{
		$body = json_decode($request->getContent(), true);

		$workshopId = (int)$workshopID;

		if ($workshopId <= 0) return $this->json(ErrorHelper::invalidRequest());

		$paramsError = ParamsChecker::check(['workshop_name', 'director_id'], $body);

		if (!empty($paramsError)) return $this->json(ErrorHelper::requestWrongParams($paramsError));
		if ((int)$body['director_id'] <= 0) return $this->json(ErrorHelper::invalidRequest());

		$em = $this->getDoctrine()->getManager();

		$workshop = $em->getRepository(Workshop::class)->find($workshopId);

		if (!$workshop) return $this->json(ErrorHelper::workshopNotFound());

		if ($workshop->getName() === $body['workshop_name'] &&
			($workshop->getDirector()
				&& $workshop->getDirector()->getId() === (int)$body['director_id']
			)
		) {
			return $this->json(ErrorHelper::nothingChange());
		}
		$director = $em->getRepository(User::class)->find((int)$body['director_id']);

		if (!$director) return $this->json(ErrorHelper::userNotFound());

		$workshop
			->setName($body['workshop_name'])
			->setDirector($director);

		$em->persist($workshop);
		$em->flush();

		return $this->json(['success' => true]);
	}

	/**
	 * @Route("/workshop/{workshopID}",methods={"DELETE"})
	 * @param $workshopID
	 *
	 * @return JsonResponse
	 */
	public function remove($workshopID)
	{
		if ((int)$workshopID <= 0) return $this->json(ErrorHelper::invalidRequest());

		$workshop = $this->getDoctrine()->getRepository(Workshop::class)->find($workshopID);

		if (!$workshop) return $this->json(ErrorHelper::workshopNotFound());

		$this->getDoctrine()->getManager()->remove($workshop);

		$this->getDoctrine()->getManager()->flush();

		return $this->json(['success' => true]);

	}

	/** @Route("/workshops",methods={"GET"}) */
	public function index()
	{
		$workshops = $this->getDoctrine()->getRepository(Workshop::class)->findAll();

		$workshopsList = array_map(function (Workshop $workshop) { return $workshop->export(); }, $workshops);

		return $this->json(['success' => true, 'items' => $workshopsList]);
	}
}
