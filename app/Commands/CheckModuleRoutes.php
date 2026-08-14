<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Spark command: php spark check:module-routes
 *
 * Walks every row in `modules` and `sub_modules`, runs each label through
 * module_slug() / submodule_slug() (see app/Helpers/menu_helper.php), and
 * checks whether a matching GET route exists in app/Config/Routes.php.
 *
 * This exists because routes are hand-written while module/sub_module rows
 * are DB-driven and slug-derived - nothing enforces that the two stay in
 * sync, so a role can end up with a sidebar link to a route that was never
 * defined (see: 'Setup' -> 'setup' 404). Run this after adding a module or
 * sub_module row, or periodically in CI, to catch that before a user does.
 *
 * NOTE: this only checks that *some* GET route exists matching the slug -
 * it does not check that the route points at a sensible controller, and it
 * does not check POST/nested routes for that module. Treat a clean run as
 * "no dangling sidebar links", not "routing is fully correct".
 */
class CheckModuleRoutes extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'check:module-routes';
    protected $description = 'Diff modules/sub_modules DB rows against defined routes to find dangling sidebar links.';

    public function run(array $params)
    {
        helper('menu');

        // ModuleModel::$DBGroup = 'gatepass' - modules/sub_modules live in
        // db_gatepass, not the default connection (hris_system). Match that
        // group here rather than hardcoding a driver/host, so this stays in
        // sync if .env credentials ever change.
        $db = Database::connect('gatepass');

        // Pull every module/sub_module row directly - deliberately NOT
        // scoped by role, since we want to catch a bad slug before any
        // role is ever granted access to it.
        $modules = $db->table('modules')->select('module_id, module_name')->get()->getResultArray();

        $subModules = $db->table('sub_modules')
            ->select('sub_module_id, module_id, sub_module_desc')
            ->get()
            ->getResultArray();

        $definedRoutes = $this->extractDefinedRoutes(APPPATH . 'Config/Routes.php');

        $missing = [];
        $checked = 0;

        foreach ($modules as $module) {
            $slug = module_slug($module['module_name']);
            $checked++;

            if (! $this->routeExists($slug, $definedRoutes)) {
                $missing[] = [
                    'type'  => 'module',
                    'label' => $module['module_name'],
                    'slug'  => $slug,
                ];
            }
        }

        foreach ($subModules as $sub) {
            $slug = submodule_slug($sub['sub_module_desc']);
            $checked++;

            if (! $this->routeExists($slug, $definedRoutes)) {
                $missing[] = [
                    'type'  => 'sub_module',
                    'label' => $sub['sub_module_desc'],
                    'slug'  => $slug,
                ];
            }
        }

        CLI::write("Checked {$checked} module/sub_module rows against " . count($definedRoutes) . " defined GET routes.", 'dark_gray');
        CLI::newLine();

        if (empty($missing)) {
            CLI::write('✓ Every module and sub_module slug has a matching route.', 'green');
            return;
        }

        CLI::write(count($missing) . ' entr' . (count($missing) === 1 ? 'y has' : 'ies have') . ' no matching route:', 'red');
        CLI::newLine();

        foreach ($missing as $row) {
            CLI::write(sprintf(
                '  [%s] "%s" -> expected slug "%s" (no route found)',
                $row['type'],
                $row['label'],
                $row['slug']
            ), 'yellow');
        }

        CLI::newLine();
        CLI::write('Fix by either:', 'dark_gray');
        CLI::write("  1. Adding \$routes->get('<slug>', 'Pages\\Something::index'); to app/Config/Routes.php, or", 'dark_gray');
        CLI::write('  2. Removing/renaming the DB row, or adding a case to the $overrides map in menu_helper.php', 'dark_gray');
    }

    /**
     * Very deliberately a regex-based scan, not a require of Routes.php -
     * we don't want to execute route registration (which depends on the
     * live RouteCollection/service container) just to introspect it.
     * This means dynamically-built routes (loops, conditionals) won't be
     * picked up; if you start generating routes programmatically, this
     * command will need a smarter approach (e.g. hitting
     * Services::routes()->getRoutes('get') inside a booted app context).
     *
     * @return string[] list of route patterns registered via ->get(...)
     */
    private function extractDefinedRoutes(string $routesFile): array
    {
        $contents = file_get_contents($routesFile);
        $routes   = [];

        // Matches: $routes->get('some/pattern', ...)
        preg_match_all('/\$routes->get\(\s*[\'"]([^\'"]+)[\'"]/', $contents, $matches);

        foreach ($matches[1] as $pattern) {
            $routes[] = trim($pattern, '/');
        }

        return $routes;
    }

    /**
     * A slug "matches" a defined route if it equals the route pattern,
     * or is the first path segment of it (so 'gatepass' matches both
     * the 'gatepass' route and covers 'gatepass/list' existing too -
     * we only require the base module route to exist, since list/store/
     * etc. are implementation detail, not sidebar links).
     */
    private function routeExists(string $slug, array $definedRoutes): bool
    {
        foreach ($definedRoutes as $pattern) {
            $base = strtok($pattern, '/');

            if ($base === $slug) {
                return true;
            }
        }

        return false;
    }
}