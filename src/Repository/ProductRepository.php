<?php
namespace App\Repository;
use App\Classe\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @extends ServiceEntityRepository<Product> *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    // On crée une méthode pour récupérer les produits en fonction de la recherche
    public function findWithSearch( Search $search)
    {
        $query = $this->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c');
        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }
        // Si le nom de la recherche est présent dans la chaine de caractère de la recherche alors on affiche les produits
        if (!empty($search->string)) {
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', '%'.$search->string.'%'); // on recherche une phrase qui contient la chaine de caractère
        }
        // Si le champ est vide alors on affiche tous les produits
        else{
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', '%');
        }
        //return $query->getQuery()->getResult();
        /*$query = $this->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c');
        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }*/
        if (!empty($search->categoryName)) {
            $query = $query
                ->andWhere('c.name LIKE :categoryName')
                ->setParameter('categoryName', '%'.$search->categoryName.'%');
        }
        if (!empty($search->productName)) {
            $query = $query
                ->andWhere('p.name LIKE :productName')
                ->setParameter('productName', '%'.$search->productName.'%');
        }
        return $query->getQuery()->getResult();
    }
//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}