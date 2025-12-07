<?php

namespace Goralys\Platform\DB\Services;

use Goralys\Platform\DB\Data\DbDto;
use Goralys\Platform\DB\Interfaces\ConnectInterface;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysConnectException;
use mysqli;
use mysqli_sql_exception;

/**
 * Service used to connect to the database
 */
class ConnectService implements ConnectInterface
{
    private GoralysLogger $logger;

    public function __construct(GoralysLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Creates and returns a connection to the database.
     * @param DbDto $credentials The necessary credentials to connect to the database.
     * @return mysqli The connection to the database.
     * @throws GoralysConnectException If the connection fails
     */
    public function connectToDatabase(DbDto $credentials): mysqli
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conn = new mysqli(
                $credentials->getHost(),
                $credentials->getUsername(),
                $credentials->getPassword(),
                $credentials->getName()
            );
            $conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception) {
            $this->logger->fatal(LoggerInitiator::PLATFORM, "Failed to connect to the database.");
            throw new GoralysConnectException("Failed to connect to the database.");
        }

        $this->logger->info(LoggerInitiator::PLATFORM, "Successfully connected to the database.");
        return $conn;
    }
}
