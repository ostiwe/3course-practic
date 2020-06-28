<?php


namespace App\Controller;


use App\Entity\Auto;
use App\Entity\Workshop;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
	/**
	 * @Route("/stats/auto/realized",methods={"GET"})
	 */
	public function relizedAuto()
	{
		$cache = new CacheController();

		if ($cache->inCache('stats.auto')) {
			return $this->json($cache->getItemFromCache('stats.auto'));
		}

		$doctrine = $this->getDoctrine();
		$autoList = $doctrine->getRepository(Auto::class)->findAll();
		$statsResponse = [];
		foreach ($autoList as $auto) {
			$exported = $auto->export();
			unset($exported['serial_number']);
			$date = (int)$exported['created_at'];
			$statsResponse[$date][] = $exported;
		}
		ksort($statsResponse);
		$statsResponseFinal = [];
		foreach ($statsResponse as $key => $value) {
			$date = date('d.m.Y', $key);
			$statsResponseFinal[$date] = [];
		}

		foreach ($statsResponse as $key => $value) {
			$date = date('d.m.Y', $key);
			$statsResponseFinal[$date][] = $value[0];
		}

		$cache->setCache('stats.auto', ['success' => true, 'data' => $statsResponseFinal]);

		return $this->json(['success' => true, 'data' => $statsResponseFinal]);
	}
}
