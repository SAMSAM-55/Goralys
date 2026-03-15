<?php

namespace Goralys\Platform\DB\Facade;

use Goralys\Platform\DB\Data\DbDto;
use Goralys\Platform\DB\Data\StmtDto;
use Goralys\Platform\DB\Services\ConnectService;
use Goralys\Platform\DB\Services\PrepareService;
use Goralys\Platform\DB\Interfaces\DbContainerInterface;
use Goralys\Platform\Loader\Services\EnvService;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysConnectException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use mysqli;
use mysqli_result;

/**
 * The database wrapper used for this project.
 * It allows for very close behavior to the default `mysqli` implementation in PHP (at least for the basics).
 * It implements:
 * - connection to the database using the `.env` configuration file
 * - statement execution/fetch
 */
class DbContainer implements DbContainerInterface
{
    private mysqli $conn;
    private LoggerInterface $logger;


    /**
     * Initializes the logger of the database container.
     * @param LoggerInterface $logger The injected logger.
     */
    public function __construct(
        LoggerInterface $logger,
    ) {
        $this->logger = $logger;
    }


    /**
     * Establish the connection to the database using the credentials inside `.env`.
     * Note that it will never return false as it throws an exception if the connection fails.
     * @return bool If the connection succeeded, true.
     * @throws GoralysConnectException Only thrown when the connection could not be established.
     */
    public function connect(): bool
    {
        $env = new EnvService();
        $service = new ConnectService($this->logger);

        $this->conn = $service->connectToDatabase(new DbDto(
            $env->getByKey("DATABASE_HOST"),
            $env->getByKey("DATABASE_NAME"),
            $env->getByKey("DATABASE_ID"),
            $env->getByKey("DATABASE_PASSWORD")
        ));

        return true;
    }

    /**
     * Executes a request on the database and returns the result.
     * It uses prepared statements to avoid SQL injection.
     * Note that the preparation of the statement is delegated to a specialized service
     * @param string $query The request to execute.
     * @param string $types The types of the statements arguments.
     * Uses the same types as the default `mysqli` implementation.
     * @param mixed $value1 The first required variable to bind.
     * @param mixed ...$args The other variables to bind (optional).
     * @return mysqli_result The result of the request.
     * @throws GoralysPrepareException|GoralysQueryException Thrown if something goes wrong during the fetch.
     */
    public function fetch(string $query, string $types, mixed $value1, ...$args): mysqli_result
    {
        $StmtData = new StmtDto(
            $query,
            $types,
            $value1,
            ...$args
        );
        $service = new PrepareService($this->logger, $this->conn);

        $stmt = $service->prepareAndBind($StmtData);

        if (!$stmt->execute()) {
            throw new GoralysQueryException("Could not run the statement");
        }

        return $stmt->get_result();
    }

    /**
     * Executes a request on the database that doesn't need any arguments (statement parameters).
     * It uses prepared statements to avoid SQL injection.
     * Note that the preparation of the statement is delegated to a specialized service
     * @param string $query The request to execute.
     * @return mysqli_result The result of the request.
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function fetchNoArgs(string $query): mysqli_result
    {
        $service = new PrepareService($this->logger, $this->conn);

        $stmt = $service->prepare($query);

        if (!$stmt->execute()) {
            throw new GoralysQueryException("Could not run the statement");
        }

        return $stmt->get_result();
    }

    /**
     * Executes a request on the database.
     * It uses prepared statements to avoid SQL injection.
     * Note that the preparation of the statement is delegated to a specialized service
     * @param string $query The request to execute.
     * @param string $types The types of the statements arguments.
     * Uses the same types as the default `mysqli` implementation.
     * @param mixed $value1 The first required variable to bind.
     * @param mixed ...$args The other variables to bind (optional).
     * @return bool `true` if the request execution was successful, `false` elsewise.
     * @throws GoralysPrepareException|GoralysQueryException Thrown if something goes wrong during the execution.
     */
    public function run(string $query, string $types, mixed $value1, ...$args): bool
    {
        $StmtData = new StmtDto(
            $query,
            $types,
            $value1,
            ...$args
        );
        $service = new PrepareService($this->logger, $this->conn);

        $stmt = $service->prepareAndBind($StmtData);

        if (!$stmt->execute()) {
            throw new GoralysQueryException("Could not run the statement");
        }

        return true;
    }

    /**
     * Executes a request on the database.
     * It uses prepared statements to avoid SQL injection.
     * Note that the preparation of the statement is delegated to a specialized service
     * @param string $query The request to execute.
     * Uses the same types as the default `mysqli` implementation.
     * @return bool `true` if the request execution was successful, `false` elsewise.
     * @throws GoralysPrepareException|GoralysQueryException Thrown if something goes wrong during the execution.
     */
    public function runNoArgs(string $query): bool
    {
        $service = new PrepareService($this->logger, $this->conn);

        $stmt = $service->prepare($query);

        if (!$stmt->execute()) {
            throw new GoralysQueryException("Could not run the statement");
        }

        return true;
    }

    /**
     * Closes the connection to the database
     */
    public function __destruct()
    {
        if (isset($this->conn)) {
            $this->conn->close();
        }
        $this->logger->info(
            LoggerInitiator::PLATFORM,
            "A DB container was destroyed, connection successfully closed"
        );
    }
}
