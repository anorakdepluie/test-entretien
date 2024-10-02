<?php

namespace App\Api\v1\Controller\DemandeClinique;

use App\Entity\DemandeClinique\Depot;
use App\Entity\DemandeClinique\Reponse;
use App\Manager\DemandeClinique\ReponseManager;
use App\Repository\DemandeClinique\DepotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * @Route("/demande-clinique")
 */
class DepotsController extends AbstractController
{
    /** @var DepotRepository */
    private $depotRepository;

    /** @var ReponseManager */
    private $reponseManager;

    public function __construct(DepotRepository $depotRepository, ReponseManager $reponseManager)
    {
        $this->depotRepository = $depotRepository;
        $this->reponseManager = $reponseManager;
    }
    /**
     * @Route("/depots", name="api_v1_depots_all", methods={"GET"})
     */
    public function all(): JsonResponse
    {
        return $this->json($this->depotRepository->findAllByReponseLaPlusRecente());
    }

    /**
     * @Route("/depots/{id}/reponses", name="api_v1_depots_creer_reponse", methods={"POST"})
     */
    public function creerReponse(Depot $depot, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->reponseManager->creer($depot, $data['titre'], $data['description'], (int) $data['type']);

        return $this->json([], Response::HTTP_CREATED);
    }

    /**
     * @Route("/depots/{depotId}/reponses/{reponseId}/validation", name="api_v1_depots_validation_reponse", methods={"POST"})
     * @ParamConverter("depot", options={"id" = "depotId"})
     * @ParamConverter("reponse", options={"id" = "reponseId"})
     */
    public function validationReponse(Depot $depot, Reponse $reponse, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->reponseManager->validation($reponse, $data['motifValidation']);

        return $this->json([], Response::HTTP_OK);
    }
}
