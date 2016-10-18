<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Mapper;

use Dot\Mapper\AbstractDbMapper;

/**
 * Class UserDetailsDbMapper
 * @package Dot\Frontend\User\Mapper
 */
class UserDetailsDbMapper extends AbstractDbMapper implements UserDetailsMapperInterface
{
    /** @var string */
    protected $idColumn = 'userId';

    /**
     * @param $userId
     * @return array|\ArrayObject|null
     */
    public function getUserDetails($userId)
    {
        return $this->select([$this->idColumn => $userId])->current();
    }

    /**
     * @param $data
     * @return int
     */
    public function insertUserDetails($data)
    {
        $data = $this->entityToArray($data);
        return $this->insert($data);
    }

    /**
     * @param $userId
     * @param $data
     * @return int
     */
    public function updateUserDetails($userId, $data)
    {
        $data = $this->entityToArray($data);
        //make sure we remove the userId field in case of an update
        if (isset($data['userId'])) {
            unset($data['userId']);
        }
        return $this->update($data, [$this->idColumn => $userId]);
    }
}