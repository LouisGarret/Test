<?php

namespace App\Controller\Api;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/')]
class PersonneController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private NormalizerInterface $normalizer)
    {
    }

    /**
     * @throws JsonException
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[OA\Response(
        response: 201,
        description: 'Retourne la personne créée',
        content: new Model(type: Personne::class)
    )]
    #[Route('personne', methods: ['POST'])]
    public function addPerson(Request $request): JsonResponse
    {
        $body = json_decode((string)$request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $personne = new Personne();

        try {
            $personne
                ->setNom($body['nom'])
                ->setPrenom($body['prenom'])
                ->setDateDeNaissance(new DateTime($body['date_de_naissance']));
        } catch (Exception $exception) {
            return new JsonResponse(data: ['error' => $exception->getMessage()], status: 400);
        }

        $this->em->persist($personne);
        $this->em->flush();

        return new JsonResponse($this->normalizer->normalize($personne, 'json'), 201);
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[Route('personnes', methods: ['GET'])]
    public function getAll(PersonneRepository $personneRepository): JsonResponse
    {
        $personnes = $personneRepository->findBy([], ['nom' => 'ASC']);
        $personnes = $this->normalizer->normalize($personnes, 'json');

        array_walk(
            $personnes,
            static fn(&$personne) => $personne['age'] = (
            new DateTime($personne['dateDeNaissance'])
            )->diff(new DateTime())->y
        );

        return new JsonResponse($personnes);
    }
}
