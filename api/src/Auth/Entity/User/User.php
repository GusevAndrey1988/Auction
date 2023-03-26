<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Services\PasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id as OrmId;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PostLoad;
use Doctrine\ORM\Mapping\Table;

#[Entity()]
#[HasLifecycleCallbacks()]
#[Table(name: 'auth_users')]
class User
{
    #[Column(type: 'auth_user_id')]
    #[OrmId()]
    private Id $id;

    #[Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $date;

    #[Column(type: 'auth_user_email', unique: true)]
    private Email $email;

    #[Column(type: 'auth_user_status', length: 16)]
    private Status $status;

    #[Column(type: 'string', nullable: true)]
    private ?string $passwordHash = null;

    #[Embedded(class: 'Token')]
    private ?Token $joinConfirmationToken = null;

    #[Embedded(class: 'Token')]
    private ?Token $passwordResetToken = null;

    #[Embedded(class: 'Token')]
    private ?Token $newEmailToken = null;

    #[Column(type: 'auth_user_email', nullable: true)]
    private ?Email $newEmail = null;

    #[Column(type: 'auth_user_role', length: 16)]
    private Role $role;

    /**
     * @var Collection<int, UserNetwork>
     */
    #[OneToMany(targetEntity: UserNetwork::class, mappedBy: 'user', cascade: ['all'], orphanRemoval: true)]
    private Collection $networks;

    private function __construct(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        Status $status
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function joinByNetwork(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        Network $network
    ): self {
        $user = new User($id, $date, $email, Status::active());
        $user->networks->add(new UserNetwork($user, $network));
        return $user;
    }

    public static function requestJoinByEmail(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->joinConfirmationToken = $token;
        return $user;
    }

    public function remove(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('Unable to remove active user.');
        }
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function changeRole(Role $newRole): void
    {
        $this->role = $newRole;
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function confirmEmailChanging(string $token, \DateTimeImmutable $date): void
    {
        if ($this->newEmail === null || $this->newEmailToken === null) {
            throw new \DomainException('Changing is not requested.');
        }
        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function requestEmailChanging(Token $token, \DateTimeImmutable $date, Email $email): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if ($this->email->isEqualTo($email)) {
            throw new \DomainException('Email is already same.');
        }
        if ($this->newEmailToken !== null && !$this->newEmailToken->isExpiredTo($date)) {
            throw new \DomainException('Changing is already requested.');
        }

        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if ($this->passwordHash === null) {
            throw new \DomainException('User does not have an old password.');
        }
        if (!$hasher->validate($current, $this->passwordHash)) {
            throw new \DomainException('Incorrect current password.');
        }
        $this->passwordHash = $hasher->hash($new);
    }

    public function resetPassword(string $token, \DateTimeImmutable $date, string $hash): void
    {
        if ($this->passwordResetToken === null) {
            throw new \DomainException('Resetting is not requested.');
        }
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function requestPasswordReset(Token $token, \DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if ($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requested.');
        }
        $this->passwordResetToken = $token;
    }

    public function attachNetwork(Network $network): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualTo($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new UserNetwork($this, $network));
    }

    /**
     * @return array<int, Network>
     */
    public function getNetworks(): array
    {
        /** @var array<int, Network> */
        return $this->networks->map(static function (UserNetwork $network) {
            return $network->getNetwork();
        })->toArray();
    }

    public function confirmJoin(string $token, \DateTimeImmutable $date): void
    {
        if ($this->joinConfirmationToken === null) {
            throw new \DomainException('Confirmation is not required.');
        }
        $this->joinConfirmationToken->validate($token, $date);
        $this->status = Status::active();
        $this->joinConfirmationToken = null;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmationToken;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    #[PostLoad()]
    public function checkEmbeds(): void
    {
        if ($this->joinConfirmationToken && $this->joinConfirmationToken->isEmpty()) {
            $this->joinConfirmationToken = null;
        }
        if ($this->passwordResetToken && $this->passwordResetToken->isEmpty()) {
            $this->passwordResetToken = null;
        }
        if ($this->newEmailToken && $this->newEmailToken->isEmpty()) {
            $this->newEmailToken = null;
        }
    }
}
