<?php

namespace App\Core;

class View {
    private static array $sections = [];
    private static ?string $currentSection = null;
    private static ?string $layout = null;
    private static array $sharedData = [];

    public static function share(string $key, $value): void {
        self::$sharedData[$key] = $value;
    }

    public static function render(string $viewPath, array $data = []): string {
        $data = array_merge(self::$sharedData, $data);
        $file = self::resolveViewPath($viewPath);

        if (!file_exists($file)) {
            return "<!-- View [{$viewPath}] not found at {$file} -->";
        }

        extract($data);
        ob_start();
        include $file;
        $content = ob_get_clean();

        // Process Blade-like syntax if file contains Blade directives
        $compiled = self::compileString($content, $data);
        return $compiled;
    }

    public static function resolveViewPath(string $view): string {
        $view = str_replace('.', '/', $view);
        $base = __DIR__ . '/../../resources/views/';
        if (file_exists($base . $view . '.blade.php')) {
            return $base . $view . '.blade.php';
        }
        if (file_exists($base . $view . '.php')) {
            return $base . $view . '.php';
        }
        return $base . $view . '.blade.php';
    }

    private static function compileString(string $template, array $data): string {
        // Handle @extends
        if (preg_match('/@extends\([\'"]([^\'"]+)[\'"]\)/', $template, $matches)) {
            $layoutView = $matches[1];
            $template = str_replace($matches[0], '', $template);

            // Extract all sections
            $sections = [];
            preg_match_all('/@section\([\'"]([^\'"]+)[\'"]\)(.*?)@endsection/s', $template, $secMatches, PREG_SET_ORDER);
            foreach ($secMatches as $sec) {
                $sections[$sec[1]] = trim($sec[2]);
            }

            // Single line sections e.g. @section('title', 'Page Title')
            preg_match_all('/@section\([\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\)/', $template, $shortSecMatches, PREG_SET_ORDER);
            foreach ($shortSecMatches as $ssec) {
                $sections[$ssec[1]] = $ssec[2];
            }

            // Render layout with the extracted sections
            $layoutFile = self::resolveViewPath($layoutView);
            if (file_exists($layoutFile)) {
                $data['__sections'] = $sections;
                extract($data);
                ob_start();
                include $layoutFile;
                $layoutContent = ob_get_clean();

                // Replace @yield('sectionName', 'default')
                $layoutContent = preg_replace_callback('/@yield\([\'"]([^\'"]+)[\'"](?:\s*,\s*[\'"]([^\'"]*)[\'"])?\)/', function ($m) use ($sections) {
                    $name = $m[1];
                    $default = $m[2] ?? '';
                    return $sections[$name] ?? $default;
                }, $layoutContent);

                return self::compileDirectives($layoutContent, $data);
            }
        }

        return self::compileDirectives($template, $data);
    }

    private static function compileDirectives(string $content, array $data): string {
        // Replace @include('view', [...])
        $content = preg_replace_callback('/@include\([\'"]([^\'"]+)[\'"](?:\s*,\s*(\[[^\]]*\]))?\)/s', function ($m) use ($data) {
            $incView = $m[1];
            return self::render($incView, $data);
        }, $content);

        return $content;
    }
}
