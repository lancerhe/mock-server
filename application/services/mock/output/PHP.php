<?php
/**
 * Mock Response Output PHP Demo
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-27
 */
namespace Service\Mock\Output;

use Service\Mock;

class PHP {

    protected $_Mock = null;

    public function __construct(Mock $Mock) {
        $this->_Mock = $Mock;
    }

    protected function _buildRequestPostString() {
        $request_post = $this->_Mock->getRequestPost();
        if ( empty( $request_post ) )
            return '';

        $request_post = str_replace(["'"],['"'], var_export($request_post, true));
        return <<<OUTPUT
curl_setopt(\$ch, CURLOPT_POST, TRUE);
curl_setopt(\$ch, CURLOPT_POSTFIELDS, http_build_query($request_post));
OUTPUT;
    }

    protected function _buildRequestUrl() {
        $url = "http://" . APPLICATION_MOCKSERVER_HOST . $this->_Mock->getUserUri();
        $request_query = $this->_Mock->getRequestQuery();
        if ( ! empty( $request_query ) ) {
            $url .= "?" . http_build_query( $request_query );
        }
        return $url;
    }

    public function output() {
        $url  = $this->_buildRequestUrl();
        $post = $this->_buildRequestPostString();
        return <<<OUTPUT
php -r '
\$ch = curl_init();
curl_setopt(\$ch, CURLOPT_URL, "$url");
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, 1);
$post
\$response = curl_exec(\$ch);
\$request  = curl_getinfo(\$ch);
curl_close(\$ch);

echo("Status Code: \$request['http_code']" . PHP_EOL);
echo("Total Time: \$request['total_time']" . PHP_EOL);
echo("Response: \$response" . PHP_EOL);
'
OUTPUT;
    }
}