<?php

declare(strict_types=1);

namespace Frontend\Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use Frontend\App\Common\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass="Frontend\Contact\Repository\MessageRepository")
 * @ORM\Table(name="contact_message")
 * @ORM\HasLifecycleCallbacks
 */
class Message extends AbstractEntity
{
    public const PLATFORM_WEBSITE = 'website';
    public const PLATFORM_ADMIN   = 'admin';

    /** @ORM\Column(name="email", type="string", length=150) */
    protected string $email = '';

    /** @ORM\Column(name="name", type="string", length=150) */
    protected string $name = '';

    /** @ORM\Column(name="subject", type="text") */
    protected string $subject = '';

    /** @ORM\Column(name="message", type="text") */
    protected string $message = '';

    /** @ORM\Column(name="platform", type="text") */
    protected string $platform = '';

    public function __construct(
        string $email,
        string $name,
        string $subject,
        string $message,
        string $platform = self::PLATFORM_WEBSITE
    ) {
        parent::__construct();

        $this->email    = $email;
        $this->name     = $name;
        $this->subject  = $subject;
        $this->message  = $message;
        $this->platform = $platform;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }
}
