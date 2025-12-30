<?php

namespace Goralys\Platform\DB\Services;

use Goralys\Platform\DB\Data\StmtDto;
use Goralys\Platform\DB\Interfaces\PrepareInterface;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\Interfaces\LoggerInterface;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use mysqli;
use mysqli_stmt;
use mysqli_sql_exception;

/**
 * The service used to prepare statements
 */
class PrepareService implements PrepareInterface
{
    private LoggerInterface $logger;
    private mysqli $conn;

    public function __construct(
        LoggerInterface $logger,
        mysqli $conn
    ) {
        $this->logger = $logger;
        $this->conn = $conn;
    }

    /**
     * Prepares a statement without parameters.
     * @param string $query The statement's request.
     * @return mysqli_stmt The prepared statement.
     * @throws GoralysPrepareException If the preparation fails.
     */
    public function prepare(string $query): mysqli_stmt
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $stmt = $this->conn->prepare($query);
        } catch (mysqli_sql_exception $e) {
            $this->logger->error(
                LoggerInitiator::PLATFORM,
                "An error occurred while preparing statement with query : " . $query .
                    ". Error : " . $e->getMessage()
            );
            throw new GoralysPrepareException("Failed to prepare statement.");
        }
        return $stmt;
    }

    /**
     * Prepare a statement and returns it.
     * Handles and log any error that could occur during preparation.
     * @param StmtDto $stmtData The necessary data to prepare the statement.
     * @return mysqli_stmt The prepared statement.
     * @throws GoralysPrepareException If the preparation fails.
     */
    public function prepareAndBind(StmtDto $stmtData): mysqli_stmt
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        if (strlen($stmtData->getTypes()) !== count($stmtData->getArgs())) {
            $this->logger->error(
                LoggerInitiator::PLATFORM,
                "Invalid param count for statement with query : " . $stmtData->getQuery()
            );
            throw new GoralysPrepareException("Failed to prepare statement.");
        }

        try {
            $stmt = $this->conn->prepare($stmtData->getQuery());

            $args = $stmtData->getArgs();
            $refs = [];

            foreach ($args as &$a) {
                $refs[] = &$a;
            }

            $stmt->bind_param(
                $stmtData->getTypes(),
                ...$refs
            );
        } catch (mysqli_sql_exception $e) {
            $this->logger->error(
                LoggerInitiator::PLATFORM,
                "An error occurred while preparing statement with query : " . $stmtData->getQuery() .
                ". Error : " . $e->getMessage()
            );
            throw new GoralysPrepareException("Failed to prepare statement.");
        }
        return $stmt;
    }
}
