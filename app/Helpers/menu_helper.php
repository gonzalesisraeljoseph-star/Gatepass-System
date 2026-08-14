<?php

if (! function_exists('module_slug')) {
    /**
     * There's no module_url / sub_module_url column in the DB, so the
     * route is derived from the label itself: "User Management" ->
     * "user-management". If a module's real route doesn't match its
     * name (e.g. "Request" -> "gatepass"), add a case to the $overrides
     * map below rather than adding a url column just for a couple of
     * exceptions - delete this override list once/if you do add a
     * dedicated column.
     */
    function module_slug(string $label): string
    {
        $overrides = [
            'Request'         => 'gatepass',
            'Approval'        => 'approvals',
            'User Management' => 'user-management',
            'Setup'           => 'setup',
        ];

        return $overrides[$label] ?? slugify($label);
    }
}

if (! function_exists('submodule_slug')) {
    /**
     * Same idea as module_slug(), but for sub_module_desc values.
     * There's no sub_module_url column, so the route is derived from
     * the description. Kept as a SEPARATE override map from
     * module_slug() because sub_module_desc values live in their own
     * namespace and could otherwise collide with a module override
     * that happens to share the same text.
     *
     * If a sub-module's real route doesn't match its description, add
     * a case below - delete this list once/if a dedicated column
     * exists.
     */
    function submodule_slug(string $label): string
    {
        $overrides = [
            'User Management' => 'user-management',
        ];

        return $overrides[$label] ?? slugify($label);
    }
}

if (! function_exists('slugify')) {
    /**
     * Shared slug logic used by module_slug() and submodule_slug().
     */
    function slugify(string $label): string
    {
        $slug = strtolower(trim($label));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        return trim($slug, '-');
    }
}