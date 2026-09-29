<?php

namespace App\Core;

class Router {
    private static array $routes = [];
    private static array $namedRoutes = [];
    private static array $groupStack = [];

    public static function get(string $uri, $action): RouteItem {
        return self::addRoute('GET', $uri, $action);
    }

    public static function post(string $uri, $action): RouteItem {
        return self::addRoute('POST', $uri, $action);
    }

    public static function put(string $uri, $action): RouteItem {
        return self::addRoute('PUT', $uri, $action);
    }

    public static function delete(string $uri, $action): RouteItem {
        return self::addRoute('DELETE', $uri, $action);
    }

    public static function any(string $uri, $action): RouteItem {
        return self::addRoute('ANY', $uri, $action);
    }

    public static function group(array $attributes, callable $callback): void {
        self::$groupStack[] = $attributes;
        call_user_func($callback);
        array_pop(self::$groupStack);
    }

    private static function addRoute(string $method, string $uri, $action): RouteItem {
        $prefix = '';
        $middlewares = [];

        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $m = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $middlewares = array_merge($middlewares, $m);
            }
        }

        $fullUri = $prefix . '/' . trim($uri, '/');
        $fullUri = '/' . trim($fullUri, '/');
        if ($fullUri !== '/') {
            $fullUri = rtrim($fullUri, '/');
        }

        $item = new RouteItem($method, $fullUri, $action, $middlewares);
        self::$routes[] = $item;
        return $item;
    }

    public static function registerNamedRoute(string $name, RouteItem $route): void {
        self::$namedRoutes[$name] = $route;
    }

    public static function getNamedUrl(string $name, array $params = []): string {
        if (!isset(self::$namedRoutes[$name])) {
            return url($name, $params);
        }
        $uri = self::$namedRoutes[$name]->getUri();
        foreach ($params as $k => $v) {
            if (strpos($uri, '{' . $k . '}') !== false) {
                $uri = str_replace('{' . $k . '}', (string)$v, $uri);
                unset($params[$k]);
            }
        }
        return url($uri, $params);
    }

    public static function dispatch(Request $request): Response {
        $method = $request->method();
        $path = $request->path();

        foreach (self::$routes as $route) {
            if ($route->matches($method, $path, $params)) {
                return $route->run($request, $params);
            }
        }

        // 404 Not Found
        if ($request->isAjax()) {
            return Response::json(['error' => 'Not Found', 'message' => 'The requested endpoint was not found'], 404);
        }
        return Response::html(view('storefront.404', ['title' => '404 - الصفحة غير موجودة']), 404);
    }
}

class RouteItem {
    private string $method;
    private string $uri;
    private $action;
    private array $middlewares;
    private ?string $name = null;

    public function __construct(string $method, string $uri, $action, array $middlewares = []) {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->middlewares = $middlewares;
    }

    public function name(string $name): self {
        $this->name = $name;
        Router::registerNamedRoute($name, $this);
        return $this;
    }

    public function middleware($middleware): self {
        $m = is_array($middleware) ? $middleware : [$middleware];
        $this->middlewares = array_merge($this->middlewares, $m);
        return $this;
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function matches(string $method, string $path, &$params = []): bool {
        if ($this->method !== 'ANY' && $this->method !== $method) {
            return false;
        }

        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $this->uri);
        $pattern = '#^' . $pattern . '$#u';

        if (preg_match($pattern, $path, $matches)) {
            $params = [];
            foreach ($matches as $k => $v) {
                if (is_string($k)) {
                    $params[$k] = urldecode($v);
                }
            }
            return true;
        }
        return false;
    }

    public function run(Request $request, array $params = []): Response {
        // Enforce CSRF protection on state-changing requests
        $csrfResponse = (new \App\Middleware\CsrfMiddleware())->handle($request);
        if ($csrfResponse instanceof Response) {
            return $csrfResponse;
        }

        // Execute Middlewares
        foreach ($this->middlewares as $mw) {
            $mwClass = $this->resolveMiddleware($mw);
            if ($mwClass) {
                $response = (new $mwClass())->handle($request);
                if ($response instanceof Response) {
                    return $response;
                }
            }
        }

        // Execute Action
        $result = null;
        if (is_callable($this->action)) {
            $result = call_user_func_array($this->action, array_merge([$request], $params));
        } elseif (is_array($this->action)) {
            [$controller, $method] = $this->action;
            $instance = new $controller();
            $result = call_user_func_array([$instance, $method], array_merge([$request], $params));
        }

        if ($result instanceof Response) {
            return $result;
        }
        if (is_array($result)) {
            return Response::json($result);
        }
        return Response::html((string)$result);
    }

    private function resolveMiddleware(string $alias): ?string {
        $map = [
            'auth' => \App\Middleware\AuthMiddleware::class,
            'admin' => \App\Middleware\AdminMiddleware::class,
            'guest' => \App\Middleware\GuestMiddleware::class,
            'locale' => \App\Middleware\SetLocaleAndCurrencyMiddleware::class,
            'visitor' => \App\Middleware\TrackVisitorMiddleware::class,
            'price_visibility' => \App\Middleware\CheckPriceVisibilityMiddleware::class,
            'csrf' => \App\Middleware\CsrfMiddleware::class,
        ];
        return $map[$alias] ?? (class_exists($alias) ? $alias : null);
    }
}
