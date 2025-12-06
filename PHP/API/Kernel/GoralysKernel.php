<?php

namespace Goralys\API\Kernel;

use ErrorException;
use Goralys\App\Utils\Toast\Controllers\ToastController;
use Goralys\Platform\DB\Facade\DbContainer;
use Goralys\Platform\Loader\Services\EnvService;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;
use Goralys\Platform\Logger\GoralysLogger;
use Goralys\Shared\Exception\DB\GoralysConnectException;
use Goralys\Shared\Exception\GoralysException;
use JetBrains\PhpStorm\NoReturn;
use Throwable;

class GoralysKernel
{
    private string $rootPath;
    private EnvService $env;
    public DbContainer $db;
    public GoralysLogger $logger;
    public ToastController $toast;

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;

        $this->startSession();
        $this->initLogger();
        $this->initEnv();
        $this->initToast();
        $this->initDb();
    }

    public function setHandlers(): void
    {
        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);
    }

    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function initLogger(): void
    {
        $this->logger = new GoralysLogger();
    }

    private function initEnv(): void
    {
        $this->env = new EnvService($this->logger);
        $this->env->load($this->rootPath);
    }

    private function initToast(): void
    {
        $this->toast = new ToastController();
    }

    private function initDb(): void
    {
        $this->db = new DbContainer($this->logger);
    }

    /**
     * @throws GoralysConnectException
     */
    public function connect(): bool
    {
        if (!isset($this->db)) {
            $this->db = new DbContainer($this->logger);
        }
        return $this->db->connect();
    }

    #[NoReturn]
    public function exceptionHandler(Throwable $e): void
    {
        $this->logger->error(
            LoggerInitiator::APP,
            "Uncaught exception: " . $e->getMessage()
        );

        if ($e instanceof GoralysException) {
            $this->toast->fatalError(500);
        } else {
            $this->toast->fatalError(500, "Une erreur inattendue s'est produite.");
        }
    }

    /**
     * @throws ErrorException
     */
    public function errorHandler($severity, $message, $file, $line): void
    {
        $this->logger->warning(
            LoggerInitiator::APP,
            "PHP Error (severity $severity) : $message — $file:$line"
        );

        // Ignore non fatal errors
        if (!in_array($severity, [E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR])) {
            return;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public function getInputByKey(string $key): string
    {

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST[$key])) {
                return trim($_POST[$key]);
            }
            return "";
        }

        $rawInput = file_get_contents("php://input");
        if (!$rawInput) {
            return "";
        }

        $decoded = json_decode($rawInput, true);

        if (!is_array($decoded)) {
            return "";
        }

        return trim($decoded[$key] ?? "");
    }

    public function run(callable $callback): void
    {
        try {
            $callback($this);
        } catch (Throwable $e) {
            $this->exceptionHandler($e);
        }
    }
}
