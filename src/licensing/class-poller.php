<?php

namespace Boxzilla\Licensing;

class Poller {

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
    public function __construct( API $api, License $license ) {
        $this->api = $api;
        $this->license = $license;
    }

    /**
     * Add hooks.
     */
    public function hook() {
        if( ! wp_next_scheduled( 'boxzilla_check_license_status' ) ) {
            wp_schedule_event( time(), 'daily', 'boxzilla_check_license_status' );
        };

        add_action( 'boxzilla_check_license_status', array( $this, 'run' ) );
    }

    /**
     * Run!
     */
    public function run() {
        // don't run if license not active
        if( ! $this->license->activated ) {
            return;
        }

        // assume valid by default, in case of server errors on our side.
        $license_still_valid = true;

        try {
            $remote_license = $this->api->get_license();
            $license_still_valid = $remote_license->valid;
        } catch( API_Exception $e ) {
            // license key wasn't found or expired
            if( in_array( $e->getApiCode(), array( 'license_invalid', 'license_expired' ) ) ) {
                $license_still_valid = false;
            }
        }

        if( ! $license_still_valid ) {
            $this->license->activated = false;
            $this->license->save();
        }

    }
}