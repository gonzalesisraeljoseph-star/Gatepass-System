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
             'Request' => 'gatepass',
        ];

        if (isset($overrides[$label])) {
            return $overrides[$label];
        }

        $slug = strtolower(trim($label));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        return trim($slug, '-');
    }
}