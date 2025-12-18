<?php

namespace SMPLFY\boilerplate;

class SyncPdfTemplateUsecase
{
    private $target_dir;

    public function __construct() {
        // GravityPDF looks here for custom templates
        $upload_dir = wp_upload_dir();
        $this->target_dir = $upload_dir['basedir'] . '/gravity_forms/pdf-templates/';
    }

    public function execute($github_raw_url, $filename) {
        // 1. Fetch the file content from GitHub
        $response = wp_remote_get($github_raw_url);

        if (is_wp_error($response)) {
            return false;
        }

        $content = wp_remote_retrieve_body($response);

        // 2. Initialize WordPress Filesystem
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            WP_Filesystem();
        }

        // 3. Ensure the GravityPDF template directory exists
        if (!$wp_filesystem->is_dir($this->target_dir)) {
            $wp_filesystem->mkdir($this->target_dir);
        }

        // 4. Write the file to the server
        $file_path = $this->target_dir . $filename;
        return $wp_filesystem->put_contents($file_path, $content);
    }
}