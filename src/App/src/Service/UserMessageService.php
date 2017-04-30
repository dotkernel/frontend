<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Service;

use Dot\Mapper\Mapper\MapperInterface;
use Dot\Mapper\Mapper\MapperManagerAwareInterface;
use Dot\Mapper\Mapper\MapperManagerAwareTrait;
use Frontend\App\Entity\UserMessageEntity;

/**
 * Class UserMessageService
 * @package Frontend\App\Service
 */
class UserMessageService implements UserMessageServiceInterface, MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    /**
     * @param UserMessageEntity $message
     * @param array $options
     * @return mixed
     */
    public function save(UserMessageEntity $message, array $options = [])
    {
        /** @var MapperInterface $mapper */
        $mapper = $this->getMapperManager()->get(UserMessageEntity::class);
        return $mapper->save($message, $options);
    }
}
