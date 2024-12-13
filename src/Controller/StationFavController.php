<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\StationUser;
use App\Entity\User;
use App\Repository\StationRepository;
use App\Repository\StationUserRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationFavController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private StationRepository $stationRepository;
    private StationUserRepository $stationUserRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        StationRepository $stationRepository,
        StationUserRepository $stationUserRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->stationRepository = $stationRepository;
        $this->stationUserRepository = $stationUserRepository;
    }

    #[Route('/mes/stations', name: 'app_mes_stations')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user)
        {
            if ($user->isBooleanChangerMdp())
            {
                return $this->redirectToRoute("app_change_mdp_force");
            }
        }
        $stations1 = $this->stationRepository->findAll();

        // Récupérer l'utilisateur connecté
        /** @var User $user */ //sans cela, $user n'est pas reconnu comme un objet de la classe "User"
        $user = $this->getUser();
        $userId = $user->getId();
        /** @var StationUserRepository $stationUserRepository */
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);

        $stationNames = [];
        $stations = $stationUserRepository->findStationsByUserId($userId);

        foreach ($stations as $station) {
            $idStation = $station["idStation"];
            $stationName = $stationUserRepository->findStationNameById($idStation)[0]["name"];
            $stationNames[] = [
                'name' => $stationName,
                'id' => $idStation
            ];

        }

        return $this->render('station_fav/index.html.twig', [
            'controller_name' => 'StationFavController',
            'station_names' => $stationNames,
            'stations1' => $stations1

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
    public function addFavorite(int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        // Vérifier si la station est déjà dans les favoris de l'utilisateur
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);
        $existingFavorite = $stationUserRepository->findOneBy([
            'idUser' => $userId,
            'idStation' => $id,
        ]);

        if ($existingFavorite) {
            $this->addFlash('info', 'Station déjà dans les favoris.');
        } else {
            $stationUser = new StationUser();
            $stationUser->setIdUser($userId);
            $stationUser->setIdStation($id);

            $this->entityManager->persist($stationUser);
            $this->entityManager->flush();

            $this->addFlash('success', 'Station ajoutée aux favoris.');
        }
        return $this->redirectToRoute('app_mes_stations');
    }
}