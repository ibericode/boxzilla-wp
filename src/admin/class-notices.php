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
        add_action('admin_notices', [ $this, 'show' ]);
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
    public function show()
    {
        foreach ($this->notices as $notice) {
            echo "<div class=\"notice notice-{$notice['type']}\"><p>{$notice['message']}</p></div>";
        }
    }
}
