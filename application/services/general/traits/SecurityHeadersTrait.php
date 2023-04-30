<?php

namespace App\services\general\traits;

trait SecurityHeadersTrait
{
    public function set_security_headers()
    {
        // Add security headers
        $this->load->helper('security');
        $this->output->set_header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        $this->output->set_header("Content-Security-Policy: img-src 'self' data:;");
        $this->output->set_header('X-Frame-Options: SAMEORIGIN');
        $this->output->set_header('X-Content-Type-Options: nosniff');
        $this->output->set_header('Referrer-Policy: strict-origin-when-cross-origin');
        $this->output->set_header("Permissions-Policy: geolocation=(self;https://*), microphone=(self;https://*), camera=(self;https://*), fullscreen=(self;), sync-xhr=(self;), usb=(self;)");
    }
}
