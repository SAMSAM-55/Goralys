<?php

namespace Goralys\App\User\Controllers;

use Goralys\App\User\Interfaces\UserControllerInterface;
use Goralys\Core\User\Repository\UserRepository;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysQueryException;

/**
 * The controller that handles the user logic.
 */
class UserController implements UserControllerInterface
{
    private LoggerInterface $logger;
    private DbContainer $db;
    private UserRepository $repo;

    /**
     * Initializes the logger and the database container used by the controller.
     * @param LoggerInterface $logger The injected logger.
     * @param DbContainer $db The injected database container.
     */
    public function __construct(
        LoggerInterface $logger,
        DbContainer $db,
    ) {
        $this->logger = $logger;
        $this->db = $db;

        $this->repo = new UserRepository($this->logger, $this->db);
    }

    /**
     * Deletes all users (except admins) from the database.
     * @return bool If the deletion was successful
     * @throws GoralysPrepareException|GoralysQueryException Only thrown if the request goes wrong.
     */
    public function clear(): bool
    {
        return $this->repo->clearAll();
    }
}
