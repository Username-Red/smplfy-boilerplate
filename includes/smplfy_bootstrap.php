<?php
/**
 * Loads specified files and all files in specified directories initialises dependencies
 */

namespace SMPLFY\boilerplate;

function bootstrap_boilerplate_plugin() {
    require_boilerplate_dependencies();

    DependencyFactory::create_plugin_dependencies();

    // Hook the role and permission logic to WordPress init
    add_action( 'init', __NAMESPACE__ . '\\smplfy_register_custom_roles' );

    // --- TEMPORARY DIAGNOSTIC BAR ---
    // This will show a notice at the top of your WP Admin to confirm permissions are active.
    add_action('admin_notices', function() {
        if (!current_user_can('administrator')) return;
        $role = get_role('manager');
        $has_cap = ($role && $role->has_cap('gravityflow_view_entries')) ? '✅ ENABLED' : '❌ DISABLED';
        echo "<div class='notice notice-info is-dismissible'><p><strong>SMT App Debug:</strong> Manager API Access is $has_cap</p></div>";
    });
}

/**
 * Registers custom roles and ensures they have the necessary API capabilities
 */
function smplfy_register_custom_roles() {
    $roles = [
        'tech'    => 'Tech',
        'manager' => 'Manager',
        'support' => 'Support'
    ];

    // These are the specific keys needed to unlock the Gravity Forms/Flow API
    $api_caps = [
        'read'                           => true,
        'gravityforms_view_entries'      => true,
        'gravityforms_edit_entries'      => true,
        'gravityflow_view_entries'       => true,
        'gravityflow_submit_entry_detail' => true, // this is so that they can submit entries
    ];

    foreach ( $roles as $slug => $name ) {
        $role_object = get_role( $slug );

        if ( ! $role_object ) {
            // If role doesn't exist, create it from scratch
            add_role( $slug, $name, $api_caps );
        } else {
            // If role exists, loop through and add any missing capabilities
            foreach ( $api_caps as $cap => $grant ) {
                if ( ! $role_object->has_cap( $cap ) ) {
                    $role_object->add_cap( $cap );
                }
            }
        }
    }
}

/**
 * When adding a new directory to the custom plugin, remember to require it here
 *
 * @return void
 */
function require_boilerplate_dependencies() {

    require_file( 'includes/enqueue_scripts.php' );
    require_file( 'admin/DependencyFactory.php' );

    require_directory( 'public/php/types' );
    require_directory( 'public/php/entities' );
    require_directory( 'public/php/repositories' );
    require_directory( 'public/php/usecases' );
    require_directory( 'public/php/adapters' );

    // 1. Include the new files (assuming they aren't auto-loaded)
    require_once plugin_dir_path(__FILE__) . '../public/php/usecases/SyncPdfTemplateUsecase.php';
    require_once plugin_dir_path(__FILE__) . '../public/php/adapters/GithubWebhookAdapter.php';

    // 2. Initialize them
    $sync_pdf_worker = new SyncPdfTemplateUsecase();
    new GithubWebhookAdapter($sync_pdf_worker);

}