<?php

use Goralys\App\HTTP\Middleware\AuthMiddleware;
use Goralys\App\HTTP\Middleware\CSRFMiddleware;
use Goralys\App\HTTP\Middleware\DbMiddleware;
use Goralys\App\HTTP\Middleware\RateLimitMiddleware;
use Goralys\App\HTTP\Middleware\RoleMiddleware;
use Goralys\App\HTTP\Middleware\ToastMiddleware;
use Goralys\App\HTTP\Request\Interfaces\RequestInterface;
use Goralys\App\Router\GoralysRouter;
use Goralys\App\Router\Options\RouterOptions;
use Goralys\App\Utils\Toast\Data\Enums\ToastType;
use Goralys\Core\User\Data\Enums\UserRole;
use Goralys\Core\User\Data\UserLoginDTO;
use Goralys\Core\User\Data\UserRegisterDTO;
use Goralys\Kernel\GoralysKernel;
use Goralys\Platform\Logger\Data\Enums\LoggerInitiator;

function createUserRoutes(GoralysRouter $router): void
{
    // ================================================
    // [SECTION] Profile
    // ================================================
    $router->get('user/profile', function (GoralysKernel $kernel) {
        $data = [
            "username"   => trim($_SESSION["current_username"]),
            "full_name"  => trim($_SESSION["current_full_name"]),
            "role"       => trim($_SESSION["current_role"]),
        ];

        $kernel->logger->info(
            LoggerInitiator::APP,
            "Accessed data of user: " . $data["username"],
        );

        $kernel->response()->json(
            [
                "success" => true,
                "data" => $data,
            ],
        );
    })
        ->middleware(...RateLimitMiddleware::for("get-profile"))
        ->middleware(...AuthMiddleware::weak());

    $router->get('user/role', function (GoralysKernel $kernel) {
        $kernel->logger->info(
            LoggerInitiator::APP,
            "Accessed data of user: " . $_SESSION["current_username"],
        );


        $kernel->response()->json(
            [
                "success" => true,
                "role" => trim($_SESSION["current_role"]),
            ],
        );
    })
        ->middleware(...RateLimitMiddleware::for("get-role"))
        ->middleware(...AuthMiddleware::weak());

    // ================================================
    // [SECTION] Auth
    // ================================================
    $router->post('user/register', function (GoralysKernel $kernel, RequestInterface $request) {
        $registerData = new UserRegisterDTO(
            $request->get("user-name"),
            $request->get("first-name") . " " . $request->get("last-name"),
            $request->get("password"),
        );

        if (!$kernel->auth->register($registerData)) {
            $kernel->deferredResponse(500)->error( // Internal Server Error
                "Une erreur interne est survenue lors de la création du compte, veuillez réessayer ultérieurement.",
            )
                ->redirect("/user/register")
                ->send();
        }

        $kernel->deferredResponse()->toast(
            ToastType::SUCCESS,
            "Création du compte",
            "Votre compte chez Goralys a bien été créé. Vous pouvez maintenant vous connecter.",
        )
            ->redirect("/user/login")
            ->send();
    }, ...RouterOptions::$INPUT::require("user-name", "password", "first-name", "last-name"))
        ->middleware(...CSRFMiddleware::form('register', '/user/register'))
        ->middleware(...DbMiddleware::require())
        ->middleware(...ToastMiddleware::flash());

    $router->post('user/login', function (GoralysKernel $kernel, RequestInterface $request) {
        $userData = new UserLoginDTO(
            $request->get("username"),
            $request->get("password"),
        );

        if (!$kernel->auth->login($userData)) {
            $kernel->deferredResponse(401)->toast(
                ToastType::ERROR,
                "Connexion",
                "Mot de passe ou identifiant incorrect.",
            )
                ->redirect("/user/login")
                ->send();
        }

        $kernel->deferredResponse()->toast(
            ToastType::SUCCESS,
            "Connexion",
            "Vous avez bien été connecté à votre compte.",
        )
            ->redirect("/subject")
            ->action("login-success")
            ->send();
    }, ...RouterOptions::$INPUT::require("username", "password"))
        ->middleware(...RateLimitMiddleware::for(
            'login',
            '/user/login',
            "Tentatives de connexion trop nombreuses, veuillez réessayer dans quelques minutes",
        ))
        ->middleware(...CSRFMiddleware::form('login', '/user/login'))
        ->middleware(...DbMiddleware::require())
        ->middleware(...ToastMiddleware::flash());

    $router->post('user/logout', function (GoralysKernel $kernel) {
        $kernel->auth->logout();
        $kernel->response()->http();
    })
        ->middleware(...RateLimitMiddleware::for('logout'))
        ->middleware(...CSRFMiddleware::form('logout'))
        ->middleware(...DbMiddleware::require());

    // ================================================
    // [SECTION] Admin actions
    // ================================================
    $router->post('users/all', function (GoralysKernel $kernel) {
        $kernel->response()->json($kernel->users->getAll());
    })
        ->middleware(...RateLimitMiddleware::for('get-all-users'))
        ->middleware(...CSRFMiddleware::form('get-all-users'))
        ->middleware(...RoleMiddleware::require(UserRole::ADMIN, true))
        ->middleware(...DbMiddleware::require());

    $router->post('users/reset-password', function (GoralysKernel $kernel, RequestInterface $request) {
        if (!$kernel->users->validatePassword($request->get("admin-password"))) {
            $kernel->deferredResponse(501)->toast( // Unauthorized
                ToastType::WARNING,
                "Mot de passe",
                "Veuillez saisir le bon mot de passe",
            )
                    ->redirect("/admin/user")
                    ->send();
        }

        if (!$kernel->users->resetPassword($request->get("target"))) {
            $kernel->deferredResponse(500)->error(
                "Le mot de passe n'a pas pu être réinitialisé.",
            )
                    ->redirect("/admin/user")
                    ->send();
        }

        $kernel->deferredResponse()->toast(
            ToastType::INFO,
            "Mot de passe",
            "Le mot de passe a bien été réinitialisé, l'utilisateur peut maintenant recréer son compte.",
        )
                ->redirect("/admin/user")
                ->send();
    }, ...RouterOptions::$INPUT::require("target", "admin-password"))
            ->middleware(...RateLimitMiddleware::for('reset-password'))
            ->middleware(...CSRFMiddleware::form('reset-password'))
            ->middleware(...RoleMiddleware::require(UserRole::ADMIN, true))
            ->middleware(...DbMiddleware::require());

    $router->post('users/delete', function (GoralysKernel $kernel, RequestInterface $request) {
        if (!$kernel->users->validatePassword($request->get("admin-password"))) {
            $kernel->deferredResponse(501)->toast( // Unauthorized
                ToastType::WARNING,
                "Mot de passe",
                "Veuillez saisir le bon mot de passe",
            )
                    ->redirect("/admin/user")
                    ->send();
        }

        if (!$kernel->users->delete($request->get("target"))) {
            $kernel->deferredResponse(500)->error(
                "L'utilisateur n'a pas pu être supprimé.",
            )
                    ->redirect("/admin/user")
                    ->send();
        }

        $kernel->deferredResponse()->toast(
            ToastType::INFO,
            "Suppression du compte",
            "L'utilisateur a bien été supprimé",
        )
                ->redirect("/admin/user")
                ->send();
    }, ...RouterOptions::$INPUT::require("target", "admin-password"))
            ->middleware(...RateLimitMiddleware::for('delete-user'))
            ->middleware(...CSRFMiddleware::form('delete-user'))
            ->middleware(...RoleMiddleware::require(UserRole::ADMIN, true))
            ->middleware(...DbMiddleware::transaction());
}
