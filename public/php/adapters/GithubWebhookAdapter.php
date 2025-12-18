<?php

namespace SMPLFY\boilerplate;

class GithubWebhookAdapter {
    private $usecase;
    // Replace this with a long random string of your choice
    private $webhook_secret = '29thd03J!';

    public function __construct(SyncPdfTemplateUsecase $usecase) {
        $this->usecase = $usecase;
        // Register the endpoint when WordPress initializes the REST API
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('smplfy/v1', '/sync-pdf', [
            'methods'  => 'POST',
            'callback' => [$this, 'handle_webhook'],
            'permission_callback' => '__return_true', // Security checked in handle_webhook
        ]);
    }

    public function handle_webhook(WP_REST_Request $request) {
        // 1. Validate the Signature from GitHub
        $signature = $request->get_header('x-hub-signature-256');
        $payload = $request->get_body();
        $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $this->webhook_secret);

        if (!hash_equals($expected_signature, $signature)) {
            return new WP_REST_Response(['message' => 'Invalid signature'], 403);
        }

        // 2. Define which files to pull from your GitHub repo
        // Replace USER, REPO, and branch (main) with your actual details
        $files = [
            'my-template.php' => 'https://github.com/Username-Red/tron-pdf/blob/master/tronpdf.php',
            'my-style.css'    => 'https://github.com/Username-Red/tron-pdf/blob/master/styles/event-ticket.css'
        ];

        foreach ($files as $name => $url) {
            $this->usecase->execute($url, $name);
        }

        return new WP_REST_Response(['message' => 'Templates updated successfully'], 200);
    }
}