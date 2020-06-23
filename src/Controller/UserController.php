<?php


namespace App\Controller;


use App\Entity\User;
use App\Entity\Workshop;
use App\ErrorHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
	/**
	 * @Route("/users",methods={"POST"})
	 * @param Request            $request
	 * @param ValidatorInterface $validator
	 *
	 * @return JsonResponse
	 */
	public function create(Request $request, ValidatorInterface $validator)
	{
		$body = json_decode($request->getContent(), true);

		$newUser = (new User())
			->setFirstName($body['first_name'] ?? '')
			->setLastName($body['last_name'] ?? '')
			->setMask(User::DEFAULT_USER);

		$errors = $validator->validate($newUser);

		if ($errors->count() > 0) {
			$errorsList = [];
			/** @var ConstraintViolation $error */
			foreach ($errors as $error) {
				$errorsList[$error->getPropertyPath()][] = $error->getMessage();
			}
			return $this->json(ErrorHelper::requestWrongParams($errorsList));
		}


		$this->getDoctrine()->getManager()->persist($newUser);
		$this->getDoctrine()->getManager()->flush();

		return $this->json(['' => '']);

	}

	/**
	 * @Route("/user/{userID}/workshop/{workshopID}",methods={"PUT"})
	 * @param         $userID
	 *
	 * @param         $workshopID
	 *
	 * @return JsonResponse
	 */
	public function setWorkshop($userID, $workshopID)
	{
		$userId = (int)$userID;
		$workshopId = (int)$workshopID;
		if ($userId <= 0 || $workshopId <= 0) return $this->json(ErrorHelper::invalidRequest());

		$em = $this->getDoctrine()->getManager();
		/** @var User $user */
		$user = $em->getRepository(User::class)->find($userId);

		if (!$user) return $this->json(ErrorHelper::userNotFound());

		if ($user->getWorkshop()->getId() === $workshopId) return $this->json(ErrorHelper::workshopAlreadySet());

		/** @var Workshop $workshop */
		$workshop = $em->getRepository(Workshop::class)->find($workshopID);
		if (!$workshop) return $this->json(ErrorHelper::workshopNotFound());

		$user->setWorkshop($workshop);

		$em->persist($user);
		$em->flush();

		return $this->json(['success' => true, 'workshop_set' => $workshop->getId()]);
	}

}
