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

	/** @Route("/user/{userId}") */
	public function getUs($userId)
	{
		var_dump($this->getDoctrine()->getRepository(User::class)->find($userId));
		return $this->json([]);
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

	/**
	 * @Route("/users",methods={"GET"})
	 */
	public function index()
	{
		$users = $this->getDoctrine()->getRepository(User::class)->findAll();
		$usersList = array_map(function (User $user) { return $user->export(); }, $users);

		return $this->json(['success' => true, 'items' => $usersList]);

	}

}
