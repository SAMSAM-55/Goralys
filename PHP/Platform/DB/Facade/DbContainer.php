<?php

namespace Goralys\Platform\DB\Facade;

use Goralys\Platform\DB\Data\DbDto;
use Goralys\Platform\DB\Data\StmtDto;
use Goralys\Platform\DB\Services\ConnectService;
use Goralys\Platform\DB\Services\PrepareService;
use Goralys\Platform\DB\Interfaces\DbContainerInterface;
use Goralys\Platform\Loader\Services\EnvService;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use Goralys\Shared\Exception\DB\GoralysConnectException;
use Goralys\Shared\Exception\DB\GoralysQueryException;
use mysqli;
use mysqli_result;

class DbContainer implements DbContainerInterface
{
    private mysqli $conn;
    private GoralysLogger $logger;

    public function __construct(
        GoralysLogger $logger,
    ) {
        $this->logger = $logger;
    }


    /**
     * @return bool
     * @throws GoralysConnectException
     */
    public function connect(): bool
    {
        $env = new EnvService($this->logger);
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
     * @param string $query
     * @param string $types
     * @param mixed $value1
     * @param mixed ...$args
     * @return mysqli_result
     * @throws GoralysPrepareException|GoralysQueryException
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
            $this->logger->error(LoggerInitiator::PLATFORM, "Could not run the statement");
            throw new GoralysQueryException("Could not run the statement");
        }

        return $stmt->get_result();
    }

    /**
     * @param string $query
     * @return mysqli_result
     * @throws GoralysPrepareException|GoralysQueryException
     */
    public function fetchNoArgs(string $query): mysqli_result
    {
        $service = new PrepareService($this->logger, $this->conn);

        $stmt = $service->prepare($query);

        if (!$stmt->execute()) {
            $this->logger->error(LoggerInitiator::PLATFORM, "Could not run the statement");
            throw new GoralysQueryException("Could not run the statement");
        }

        return $stmt->get_result();
    }

    /**
     * @param string $query
     * @param string $types
     * @param mixed $value1
     * @param mixed ...$args
     * @return bool
     * @throws GoralysPrepareException|GoralysQueryException
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
