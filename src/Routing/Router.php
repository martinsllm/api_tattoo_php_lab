<?php

namespace App\Routing;

class Router
{
    public function __construct(private array $routes)
    {
    }

    public function match(string $method, string $path): ?array
    {
       $path = rtrim($path, '/');
       $path = $path === '' ? '/' : $path;

       if (!isset($this->routes[$method])) {
            return null;
       }

       foreach ($this->routes[$method] as $pattern => $definition) {        
            $patternSegments = explode('/', trim($pattern, '/'));
            $pathSegments = $path === '/' ? [] : explode('/', trim($path, '/'));
            
            if (count($patternSegments) !== count($pathSegments)) {
                continue;
            }

            $params = [];

            for ($i = 0; $i < count($patternSegments); $i++) {
                $patternPart = $patternSegments[$i];
                $pathPart = $pathSegments[$i];

                if ($patternPart === '{id}') {
                    if (!ctype_digit($pathPart)) {
                        continue 2;
                    }
                    $params['id'] = $pathPart;
                    continue;
                }

                if ($patternPart !== $pathPart) {
                    continue 2;
                }
            }   

            return [
                'handler' => $definition['handler'],
                'params' => $params,
            ]; 
       }

       return null;
    }
}