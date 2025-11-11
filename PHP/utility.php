<?php

declare(strict_types=1);

namespace Goralys\Utility;

use Goralys\Config\Config;

final class GoralysUtility
{
    /**
    * Returns the full name of a user based on a short id.
    *
    * @param string $userId The short id, e.g. “s.saubion5”.
    *
    * @return string The formatted name, e.g. “SAUBION S.”.
    */
    final public static function formatUserId(string $userId): string
    {
        // Expected format: first initial, dot, last name, optional digits.
        if (preg_match('/^([a-z])\.([a-z]+)\d*$/i', $userId, $matches)) {
            $firstInitial = strtoupper($matches[1]);
            $lastName     = strtoupper($matches[2]);

            return $lastName . ' ' . $firstInitial . '.';
        }

        // Return the original value if it does not match the expected format.
        return $userId;
    }

    /**
    * Enable server-side backend (PHP) to use the toast system of the client-side frontend (JS)
    * @param string $toast_type The toast type : error, warning, success or info
    * @param string $toast_title The toast title
    * @param string $toast_message The toast message
    * @param string $to_page The page to redirect to (if $js = false)
    * @param bool $js Defines if the function should redirect (using $to_page) or output a JSON object (if using JS)
    * @return void
    */
    final public static function showToast(
        string $toast_type,
        string $toast_title,
        string $toast_message,
        string $to_page = "index.html",
        bool $js = false
    ): void {

        // Normalize values
        $toast_type    = trim($toast_type);
        $toast_title   = trim($toast_title);
        $toast_message = trim($toast_message);

        if ($js) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }

            echo json_encode([
                "toast" => true,
                "toast_type" => $toast_type,
                "toast_title" => $toast_title,
                "toast_message" => $toast_message,
                "redirect" => Config::FOLDER . $to_page
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $base = Config::FOLDER . $to_page;
        $query = http_build_query([
        "toast"         => "true",
        "toast-type"    => $toast_type,
        "toast-title"   => $toast_title,
        "toast-message" => $toast_message,
        ]);

        echo "<script type='text/javascript'>
        window.location.href = window.location.origin + '$base' + '?$query';
    </script>";
    }

    /**
    * Stores information about a topic in the session.
    *
    * @param string $topicId     The database id of the topic.
    * @param int    $topicIndex  Either 1 or 2 – the index used in the session key.
    *
    * @return void
    */
    final public static function cacheStudentTopicsInfo(string $topicId, int $topicIndex): void
    {
        $conn = Config::connectToDatabase();

        $query = 'SELECT * FROM saje5795_goralys.topics WHERE id = ?';
        $stmt  = $conn->prepare($query);
        $stmt->bind_param('s', $topicId);

        if (! $stmt->execute()) {
            self::showToast(
                'error',
                'Erreur',
                'Une erreur interne est survenue, veuillez réessayer ultérieurement'
            );
        }

        $row = $stmt->get_result()->fetch_assoc();

        $_SESSION["user-topic-id-$topicIndex"]   = $topicId;
        $_SESSION["user-topic-$topicIndex"]     = $row['name'];
        $_SESSION["user-teacher-$topicIndex"]  = self::formatUserId($row['teacher_id']);

        $stmt->close();
        $conn->close();
    }

    /**
    * Verifies a CSRF token.
    *
    * @param string $token Optional token to verify; if omitted the POST value will be used.
    *
    * @return bool TRUE if the token is valid, FALSE otherwise.
    */
    final public static function verifyCSRF(string $token = "", bool $js = false): bool
    {
        @session_start();

        if (!isset($_POST['csrf-token']) && $token === "") {
            self::showToast(
                'error',
                "Sécurité",
                "Une erreur interne est survenue et l'opération a été suspendue pour votre sécurité.",
                js: $js
            );
            error_log("Invalid csrf token was sent");
            return false;
        }

        $csrfRequestToken = $token !== "" ? trim($token) : trim($_POST['csrf-token']) ?? '';
        $csrfSessionToken = $_SESSION['csrf-token'] ?? '';

        if (
            $csrfRequestToken === '' ||
            $csrfSessionToken === '' ||
            ! hash_equals($csrfSessionToken, $csrfRequestToken)
        ) {
            http_response_code(403);

            self::showToast(
                'error',
                'Lien suspect',
                'Ce lien semble provenir d\'un site externe. Ne faites pas confiance à cette source.',
                js: $js
            );

            return false;
        }

        return true;
    }
}
