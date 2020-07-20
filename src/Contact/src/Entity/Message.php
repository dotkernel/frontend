<?php

/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\Contact\Entity;

use Frontend\App\Common\AbstractEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Message
 * @package Frontend\Frontend\Contact\Entity
 *
 * @ORM\Entity(repositoryClass="Frontend\Contact\Repository\MessageRepository")
 * @ORM\Table(name="contact_message")
 * @ORM\HasLifecycleCallbacks
 * @package Frontend\Contact\Entity
 */
class Message extends AbstractEntity
{
    public const PLATFORM_WEBSITE = 'website';
    public const PLATFORM_ADMIN = 'admin';

    /**
     * @ORM\Column(name="email", type="string", length=150)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(name="name", type="string", length=150)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="subject", type="text")
     * @var string
     */
    protected $subject;

    /**
     * @ORM\Column(name="message", type="text")
     * @var string
     */
    protected $message;

    /**
     * @ORM\Column(name="platform", type="text")
     * @var string
     */
    protected $platform;

    /**
     * Message constructor.
     * @param string $email
     * @param string $name
     * @param string $subject
     * @param string $message
     * @param string $platform
     */
    public function __construct(
        string $email,
        string $name,
        string $subject,
        string $message,
        string $platform = self::PLATFORM_WEBSITE
    ) {
        parent::__construct();

        $this->email = $email;
        $this->name = $name;
        $this->subject = $subject;
        $this->message = $message;
        $this->platform = $platform;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     */
    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }
}
