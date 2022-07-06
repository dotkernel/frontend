<?php
declare(strict_types=1);

use Frontend\User\Entity\UserRememberMe;
use Phinx\Migration\AbstractMigration;

final class RememberUserSchema extends AbstractMigration
{
    protected string $rememberUser = 'user_remember_me';

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table(
            $this->rememberUser,
            ['id' => false, 'primary_key' => ['uuid'], 'collation' => 'utf8mb4_general_ci']
        )
            ->addColumn('uuid', 'binary', ['null' => false, 'limit' => 16])
            ->addColumn('userUuid', 'binary', ['null' => false, 'limit' => 16])
            ->addColumn('rememberMeToken', 'string', ['null' => true, 'limit' => 100])
            ->addColumn('deviceModel', 'string', ['null' => true, 'default' => null, 'limit' => 255])
            ->addColumn('expireDate', 'timestamp', ['null' => true])
            ->addColumn('created', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated', 'timestamp', ['null' => true])

            ->create();
    }
}
