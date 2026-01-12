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

    foreach ( $roles as $slug => $name ) {
        if ( ! get_role( $slug ) ) {
            add_role( $slug, $name, array( 'read' => true ) );
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

