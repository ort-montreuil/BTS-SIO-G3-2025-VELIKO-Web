<?php

namespace App\Controller;

use App\Entity\StationUser;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationFavController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private StationRepository $stationRepository;

    public function __construct(EntityManagerInterface $entityManager, StationRepository $stationRepository)
    {
        $this->entityManager = $entityManager;
        $this->stationRepository = $stationRepository;
    }

    #[Route('/mes/stations', name: 'app_mes_stations')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        /** @var User $user */ //sans cela, $user n'est pas reconnu comme un objet de la classe "User"
        $user = $this->getUser();
        $userId = $user->getId();
        /** @var StationUserRepository $stationUserRepository */
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);

        $stationNames = [];
        for ($i = 0; $i < count($stationUserRepository->findStationsByUserId($userId)); $i++) {
            $idStation = $stationUserRepository->findStationsByUserId($userId)[$i]["idStation"];
            $stationName = $stationUserRepository->findStationNameById($idStation)[0]["name"];
            $stationNames[] = [
                'name' => $stationName,
                'id' => $idStation
            ];
        }

        return $this->render('station_fav/index.html.twig', [
            'controller_name' => 'MesStationsController',
            'station_names' => $stationNames
        ]);
    }
    #[Route('/station/delete/{id}', name: 'app_station_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        /** @var StationUserRepository $stationUserRepository */
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);

        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $idStation = $request->get("id");

        if ($idStation) {
//            $this->entityManager->remove($idStation); impossible, car on n'a que des id dans l'entité stationUser pas d'object user et station
            $stationUserRepository->deleteStationByStationId($idStation);
            $this->entityManager->flush();

            $this->addFlash('Succès', 'Station supprimée.');
        } else {
            $this->addFlash('Erreur', 'Station non trouvée.');
        }

        return $this->redirectToRoute('app_mes_stations');

    }
    #[Route('/station/add_favorite/{id}', name: 'app_add_favorite', methods: ['POST'])]
    public function addFavorite(int $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $station = $this->stationRepository->find($id);

        if ($station) {
            $stationUser = new StationUser();
            $stationUser->setIdStation($id);
            $stationUser->setIdUser($userId);

            $this->entityManager->persist($stationUser);
            $this->entityManager->flush();

            $this->addFlash('success', 'Station added to favorites.');
        } else {
            $this->addFlash('error', 'Station not found.');
        }

        return $this->redirectToRoute('app_mes_stations');
    }

}
