<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! function_exists( 'getallheaders' ) ) {
    function getallheaders() {
        $headers = [];
        foreach ( $_SERVER as $name => $value ) {
            if ( substr( $name, 0, 5 ) === 'HTTP_' ) {
                // Convert HTTP_HEADER_NAME to Header-Name.
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }
}
