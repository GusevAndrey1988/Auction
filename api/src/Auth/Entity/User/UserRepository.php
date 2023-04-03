<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    private EntityRepository $repo;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(User::class);
        $this->repo = $repo;
    }

    public function hasByNetwork(Network $identity): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->innerJoin('t.networks', 'n')
            ->andWhere('n.network = :name and n.identity = :identity')
            ->setParameter(':name', $identity->getName())
            ->setParameter(':identity', $identity->getIdentity())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function findByJoinConfirmToken(string $token): ?User
    {
        /** @psalm-var User|null */
        return $this->repo->findOneBy(['joinConfirmToken.value' => $token]);
    }

    public function findByPasswordResetToken(string $token): ?User
    {

        /** @psalm-var User|null */
        return $this->repo->findOneBy(['passwordResetToken.value' => $token]);
    }

    public function findByNewEmailToken(string $token): ?User
    {
        /** @psalm-var User|null */
        return $this->repo->findOneBy(['newEmailToken.value' => $token]);
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }

    /**
     * @throws \DomainException
     */
    public function get(Id $userId): User
    {
        if (!$user = $this->repo->find($userId->getValue())) {
            throw new \DomainException('User is not found.');
        }

        /** @var User $user */
        return $user;
    }

    /**
     * @throws \DomainException
     */
    public function getByEmail(Email $email): User
    {
        if (!$user = $this->repo->findOneBy(['email' => $email->getValue()])) {
            throw new \DomainException('User is not found.');
        }

        /** @var User $user */
        return $user;
    }
}
