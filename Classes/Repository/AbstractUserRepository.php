<?php
declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Repository;

/*
 * This file is part of the openid_connect extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractUserRepository implements UserRepositoryInterface
{
    /**
     * @param string $username
     * @return false|array
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getUser(string $username)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->eq(
                    'username',
                    $queryBuilder->createNamedParameter($username)
                )
            );
        $statement = $queryBuilder->execute();
        return $statement->fetchAssociative();
    }

    /**
     * @param array $user
     * @return void
     */
    public function createUser(array $user): void
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->insert($this->tableName)
            ->values($user)
            ->execute();
    }

    /**
     * @param array $user
     * @return void
     */
    public function updateUser(array $user): void
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->update($this->tableName)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($user['uid'], \PDO::PARAM_INT)
                )
            );
        foreach ($user as $key => $value) {
            $queryBuilder->set($key, $value);
        }
        $queryBuilder->execute();
    }

    /**
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->getRestrictions()->removeAll();
        $deletedRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
        $queryBuilder->getRestrictions()->add($deletedRestriction);
        return $queryBuilder;
    }
}
