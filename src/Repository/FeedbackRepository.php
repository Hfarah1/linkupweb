<?php

namespace App\Repository;

use App\Entity\Feedback;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feedback>
 */
class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

//    /**
//     * @return Feedback[] Returns an array of Feedback objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Feedback
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @return Feedback[] Returns an array of Feedback objects for a specific rencontre
     */
    public function findByRencontre(int $id_rencontre): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id_rencontre = :val')
            ->andWhere('f.reply_to IS NULL')
            ->setParameter('val', $id_rencontre)
            ->orderBy('f.date_commentaire', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * @return Feedback[] Returns an array of Feedback objects that are replies to a specific feedback
     */
    public function findReplies(int $id_feedback): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.reply_to = :val')
            ->setParameter('val', $id_feedback)
            ->orderBy('f.date_commentaire', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * @return int Returns the count of feedbacks for a specific rencontre
     */
    public function countByRencontre(int $id_rencontre): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id_feedback)')
            ->andWhere('f.id_rencontre = :val')
            ->setParameter('val', $id_rencontre)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return Feedback[] Returns an array of Feedback objects for a specific rencontre with utilisateur data
     */
    public function findByRencontreWithUtilisateur(int $id_rencontre): array
    {
        $feedbacks = $this->findByRencontre($id_rencontre);
        $utilisateurRepo = $this->getEntityManager()->getRepository(Utilisateur::class);
        
        foreach ($feedbacks as $feedback) {
            $utilisateur = $utilisateurRepo->find($feedback->getIdUtilisateur());
            if ($utilisateur) {
                $feedback->setUtilisateur($utilisateur);
            }
        }
        
        return $feedbacks;
    }
    
    /**
     * @return Feedback[] Returns an array of Feedback objects that are replies to a specific feedback with utilisateur data
     */
    public function findRepliesWithUtilisateur(int $id_feedback): array
    {
        $replies = $this->findReplies($id_feedback);
        $utilisateurRepo = $this->getEntityManager()->getRepository(Utilisateur::class);
        
        foreach ($replies as $reply) {
            $utilisateur = $utilisateurRepo->find($reply->getIdUtilisateur());
            if ($utilisateur) {
                $reply->setUtilisateur($utilisateur);
            }
        }
        
        return $replies;
    }
}
