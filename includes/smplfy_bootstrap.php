<?php
/**
 *  Loads specified files and all files in specified directories initialises dependencies
 */

namespace SMPLFY\boilerplate;

function bootstrap_boilerplate_plugin() {
    require_boilerplate_dependencies();

    DependencyFactory::create_plugin_dependencies();

    add_action( 'init', __NAMESPACE__ . '\\smplfy_register_custom_roles' );
}

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
        'gravityflow_submit_entry_detail' => true, // Allows them to process steps
    ];

    foreach ( $roles as $slug => $name ) {
        if ( ! get_role( $slug ) ) {
            // If role doesn't exist, create it with the API caps
            add_role( $slug, $name, $api_caps );
        } else {
            // If role ALREADY exists, make sure it HAS the API caps
            $role_object = get_role( $slug );
            foreach ( $api_caps as $cap => $grant ) {
                $role_object->add_cap( $cap );
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

