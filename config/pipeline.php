<?php

declare(strict_types=1);

use Dot\DebugBar\Middleware\DebugBarMiddleware;
use Dot\ErrorHandler\ErrorHandlerInterface;
use Dot\ResponseHeader\Middleware\ResponseHeaderMiddleware;
use Dot\Session\SessionMiddleware;
use Frontend\App\Middleware\RememberMeMiddleware;
use Mezzio\Application;
use Mezzio\Cors\Middleware\CorsMiddleware;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Helper\ServerUrlMiddleware;
use Mezzio\Helper\UrlHelperMiddleware;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\ImplicitHeadMiddleware;
use Mezzio\Router\Middleware\ImplicitOptionsMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Psr\Container\ContainerInterface;
use Frontend\App\Middleware\TranslatorMiddleware;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Frontend\App\Middleware\AuthMiddleware;
use Frontend\Slug\Middleware\SlugMiddleware;

/**
 * Setup middleware pipeline:
 */
return static function (Application $application, MiddlewareFactory $middlewareFactory, ContainerInterface $container) : void {
    // The error handler should be the first (most outer) middleware to catch
    // all Exceptions.
    $application->pipe(DebugBarMiddleware::class);
    $application->pipe(ErrorHandlerInterface::class);
    $application->pipe(SessionMiddleware::class);
    $application->pipe(ServerUrlMiddleware::class);
    $application->pipe(CorsMiddleware::class);
    // Pipe more middleware here that you want to execute on every request:
    // - bootstrapping
    // - pre-conditions
    // - modifications to outgoing responses
    //
    // Piped Middleware may be either callables or service names. Middleware may
    // also be passed as an array; each item in the array must resolve to
    // middleware eventually (i.e., callable or service name).
    //
    // Middleware can be attached to specific paths, allowing you to mix and match
    // applications under a common domain.  The handlers in each middleware
    // attached this way will see a URI with the matched path segment removed.
    //
    // i.e., path of "/api/member/profile" only passes "/member/profile" to $apiMiddleware
    // - $app->pipe('/api', $apiMiddleware);
    // - $app->pipe('/docs', $apiDocMiddleware);
    // - $app->pipe('/files', $filesMiddleware);
    // Slug Middleware must be triggered before RouteMiddleware!
    $application->pipe(SlugMiddleware::class);
    // Register the routing middleware in the middleware pipeline.
    // This middleware registers the Mezzio\Router\RouteResult request attribute.
    $application->pipe(RouteMiddleware::class);
    $application->pipe(ResponseHeaderMiddleware::class);
    // The following handle routing failures for common conditions:
    // - HEAD request but no routes answer that method
    // - OPTIONS request but no routes answer that method
    // - method not allowed
    // Order here matters; the MethodNotAllowedMiddleware should be placed
    // after the Implicit*Middleware.
    $application->pipe(ImplicitHeadMiddleware::class);
    $application->pipe(ImplicitOptionsMiddleware::class);
    $application->pipe(MethodNotAllowedMiddleware::class);
    // Seed the UrlHelper with the routing results:
    $application->pipe(UrlHelperMiddleware::class);
    // Add more middleware here that needs to introspect the routing results; this
    // might include:
    //
    // - route-based authentication
    // - route-based validation
    // - etc.
    $application->pipe(TranslatorMiddleware::class);
    $application->pipe(RememberMeMiddleware::class);
    $application->pipe(AuthMiddleware::class);
    $application->pipe(ForbiddenHandler::class);
    $application->pipe(RbacGuardMiddleware::class);
    // Register the dispatch middleware in the middleware pipeline
    $application->pipe(DispatchMiddleware::class);
    // At this point, if no Response is returned by any middleware, the
    // NotFoundHandler kicks in; alternately, you can provide other fallback
    // middleware to execute.
    $application->pipe(NotFoundHandler::class);
};
