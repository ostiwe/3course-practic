<?php

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use App\ErrorHelper;
use App\ParamsChecker;
use App\Services\RequestStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
	/**
	 * @Route("/auth/info", methods={"POST"})
	 * @param RequestStorage $storage
	 *
	 * @return JsonResponse
	 */
	public function index(RequestStorage $storage)
	{
		/** @var AccessToken $token */
		$token = $storage->get('token_info');
		/** @var User $user */
		$user = $storage->get('user_info');


		return $this->json([
			'success' => true,
			'data' => [
				'user_info' => $user->export(),
				'access_token' => $token->export(),
			],
		]);
	}

	/**
	 * @Route("/auth/login", methods={"POST"})
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function login(Request $request)
	{
		$body = json_decode($request->getContent(), true);
		$errors = ParamsChecker::check(['login', 'password'], $body);

		if (count($errors) > 0) return $this->json(ErrorHelper::requestWrongParams($errors));

		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository(User::class)->findOneBy([
			'login' => $body['login'],
		]);
		if (!$user) return $this->json(ErrorHelper::userNotFound());

		if (!password_verify($body['password'], $user->getPassword()))
			return $this->json(ErrorHelper::authorizationFailed(ErrorHelper::AUTH_FAILED_PASSWORD));

		$newToken = (new AccessToken())
			->setCreatedAt(time())
			->setExpiredAt(time() + 86600)
			->setMask($user->getMask())
			->generate();

		$user->addAccessToken($newToken);

		$em->persist($newToken);
		$em->persist($user);

		$em->flush();

		return $this->json([
			'success' => true,
			'data' => [
				'user_info' => $user->export(),
				'access_token' => $newToken->export(),
			],
		]);
	}

	/**
	 * @Route("/auth/register",methods={"POST"})
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function register(Request $request)
	{
		$body = json_decode($request->getContent(), true);
		$errors = ParamsChecker::check([
			'login', 'password',
			'first_name',
			'last_name'], $body);
		if (count($errors) > 0) return $this->json(ErrorHelper::requestWrongParams($errors));

		$userRepo = $this->getDoctrine()->getRepository(User::class);

		$existLoginUser = $userRepo->findBy([
			'login' => $body['login'],
		]);

		if ($existLoginUser)
			return $this->json(ErrorHelper::registerError(ErrorHelper::REGISTER_USER_ALREADY_EXIST));


		$newUser = (new User())
			->setMask(User::DEFAULT_USER)
			->setPassword(password_hash($body['password'], PASSWORD_DEFAULT))
			->setLogin($body['login'])
			->setLastName($body['last_name'])
			->setFirstName($body['first_name']);

		$this->getDoctrine()->getManager()->persist($newUser);


		$this->getDoctrine()->getManager()->flush();
		return $this->json([
			'success' => true,
		]);
	}

	/**
	 * @Route("/auth/logout",methods={"POST"})
	 * @param RequestStorage $storage
	 *
	 * @return JsonResponse
	 */
	public function logOut(RequestStorage $storage)
	{

		$token = $storage->get('token_info');

		$this->getDoctrine()->getManager()->remove($token);
		$this->getDoctrine()->getManager()->flush();


		return $this->json(['success' => true,]);
	}

}
