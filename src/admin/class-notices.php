<?php

namespace Boxzilla\Admin;

class Notices
{
    /**
     * @var array
     */
    protected $notices = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_notices', [ $this, 'action_admin_notices' ], 10, 0);
    }

    /**
     * @param $message
     * @param $type
     *
     * @return $this
     */
    public function add($message, $type = 'updated')
    {
        $this->notices[] = [
            'message' => $message,
            'type'    => $type,
        ];

        return $this;
    }

    /**
     * Output the registered notices
     */
    public function admin_notices(): void
    {
        $allowed_html = [ 'strong' => [], 'em' => [], 'a' => [ 'href' => [] ] ];
        foreach ($this->notices as $notice) {
            echo "<div class=\"notice notice-", esc_attr($notice['type']), "><p>", wp_kses($notice['message'], $allowed_html), "</p></div>";
        }
    }
}
