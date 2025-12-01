<?php

namespace Goralys\Platform\DB\Services;

use Goralys\Platform\DB\Data\StmtDto;
use Goralys\Platform\DB\Interfaces\PrepareInterface;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Shared\Exception\DB\GoralysPrepareException;
use mysqli;
use mysqli_stmt;
use mysqli_sql_exception;

class PrepareService implements PrepareInterface
{
    private GoralysLogger $logger;
    private mysqli $conn;

    public function __construct(
        GoralysLogger $logger,
        mysqli $conn
    ) {
        $this->logger = $logger;
        $this->conn = $conn;
    }

    /**
     * Prepare a statement and returns it.
     * Handles and log any error that could occur during preparation.
     * @param StmtDto $stmtData The necessary data to prepare the statement
     * @return mysqli_stmt The prepared statement
     * @throws GoralysPrepareException
     */
    public function prepareAndBind(StmtDto $stmtData): mysqli_stmt
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $stmt = $this->conn->prepare($stmtData->getQuery());

            $stmt->bind_param(
                $stmtData->getTypes(),
                ...$stmtData->getArgs()
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
