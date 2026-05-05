<?php

namespace Boxzilla\Licensing;

if (! defined('ABSPATH')) {
    exit;
}

class Poller
{
    public const HOOK = 'boxzilla_check_license_status';

    /**
     * @var API
     */
    protected $api;

    /**
     * @var License
     */
    protected $license;

    /**
     * Poller constructor.
     *
     * @param API $api
     * @param License $license
     */
    public function __construct(API $api, License $license)
    {
        $this->api     = $api;
        $this->license = $license;
    }

    /**
     * Add hooks.
     */
    public function init()
    {
        if (! wp_next_scheduled(self::HOOK)) {
            wp_schedule_event(time(), 'daily', self::HOOK);
        }

        add_action(self::HOOK, [ $this, 'run' ]);
    }

    public static function deactivate()
    {
        wp_clear_scheduled_hook(self::HOOK);
    }

    /**
     * Run!
     */
    public function run()
    {
        // don't run if license not active
        if (! $this->license->activated) {
            return;
        }

        // assume valid by default, in case of server errors on our side.
        $license_still_valid = true;

        try {
            $remote_license      = $this->api->get_license();
            $license_still_valid = $remote_license->valid;
        } catch (API_Exception $e) {
            // license key wasn't found or expired
            if (in_array($e->getApiCode(), [ 'license_invalid', 'license_expired' ], true)) {
                $license_still_valid = false;
            }
        }

        if (! $license_still_valid) {
            $this->license->activated = false;
            $this->license->save();
        }
    }
}
