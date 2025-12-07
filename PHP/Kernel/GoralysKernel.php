<?php

namespace Goralys\Kernel;

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

/**
 * The kernel used by the API to access the database, environment and toast controller.
 */
class GoralysKernel
{
    private string $rootPath;
    private EnvService $env;
    public DbContainer $db;
    public GoralysLogger $logger;
    public ToastController $toast;

    /**
     * Initializes the kernel and all of its members.
     * @param string $rootPath The path to the .env file and that is considered to be the root path for the kernel.
     */
    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;

        $this->startSession();
        $this->initLogger();
        $this->initEnv();
        $this->initToast();
        $this->initDb();
    }

    /**
     * Sets the exceptions and errors handlers to be the ones from the `GoralysKernel`.
     * @return void
     */
    public function setHandlers(): void
    {
        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);
    }

    /**
     * Starts the PHP session if it is not already started.
     * @return void
     */
    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Initializes the logger of the kernel.
     * The logger used is a `GoralysLogger` which is a custom logger made specially for this project.
     * @return void
     */
    private function initLogger(): void
    {
        $this->logger = new GoralysLogger();
    }

    /**
     * Loads the environment variables inside $_ENV using `DotEnv`.
     * The path to the .env file is supposed to be `$rootPath`.
     * @return void
     */
    private function initEnv(): void
    {
        $this->env = new EnvService($this->logger);
        $this->env->load($this->rootPath);
    }

    /**
     * Initializes the toast controller sed by the kernel.
     * @return void
     */
    private function initToast(): void
    {
        $this->toast = new ToastController();
    }

    /**
     * Initializes the database container of the kernel.
     * Note that the database connection won't be established until the `connect` method of the kernel is called.
     * @return void
     */
    private function initDb(): void
    {
        $this->db = new DbContainer($this->logger);
    }

    /**
     * Connects the kernel to the database.
     * @throws GoralysConnectException Throws an exception if the connection with the database could not be established.
     */
    public function connect(): bool
    {
        if (!isset($this->db)) {
            $this->db = new DbContainer($this->logger);
        }
        return $this->db->connect();
    }

    /**
     * The custom exception handler for the kernel.
     * It handles `GoralysException` and its instances as "normal" errors.
     * Therefore for other exceptions, it toasts them as unexpected.
     * @param Throwable $e The thrown exception.
     * @return void
     */
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
     * The custom error handler for the kernel.
     * It ignores errors considered "non-fatal":
     * - `E_ERROR`
     * - `E_USER_ERROR`
     * - `E_CORE_ERROR`
     * - `E_COMPILE_ERROR`
     * - `E_RECOVERABLE_ERROR`
     * @param int $severity The severity of the error.
     * @param string $message The error's message.
     * @param string $file The file where the error occurred.
     * @param int $line The line that caused the error.
     * @return void
     * @throws ErrorException Only thrown when the exception is considered fatal.
     */
    public function errorHandler(int $severity, string $message, string $file, int $line): void
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

    /**
     * Gets an input from either `$_POST` or the `php://input` file if the endpoint is fetched from JS/TS.
     * If an input is not found (for both `$_POST` and `php://input`), it returns an empty string.
     * @param string $key The name of the input.
     * @return string The retrieved input.
     */
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


    /**
     * Runs the provided function and catches any exception to handle it.
     * @param callable $callback The function to execute.
     * @return void
     */
    public function run(callable $callback): void
    {
        try {
            $callback($this);
        } catch (Throwable $e) {
            $this->exceptionHandler($e);
        }
    }
}
