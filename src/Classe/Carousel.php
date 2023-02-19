<?php
namespace App\Classe;
use Doctrine\ORM\EntityManagerInterface;

class Carousel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getImages(): array
    {
        // Récupérer toutes les images depuis la base de données
        // Retourner les images
        return $this->em->getRepository(Carousel::class)->findAll();
    }
}
