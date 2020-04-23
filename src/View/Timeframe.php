<?php

namespace CommonsBooking\View;

class Timeframe extends View
{

    protected static $template = 'item/index.html.twig';

    public static function content(\WP_Post $post) {
        echo self::render(self::$template, [
            'post' => $post
        ]);
    }

}