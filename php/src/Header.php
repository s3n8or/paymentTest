<?

class Header {

    public $contentType = 'applicaton/json';
    public $statusCode = 200;
    public $charset = 'utf-8';

    /**
     * Sets the header with the values it has saved in it's properties. Sort of a convenience so we don't have to do it manually each time.
     */
    public function setHeader() {
        $http = "HTTP/1.1";
        $statusCode = $this->_getStatusCodeText($this->statusCode);
        $contentType = "Content-Type: {$this->contentType}";
        $charset = "charset={$this->charset}";

        $header = implode(' ', [$http, $statusCode]);
        $header = implode('; ', [$header, $contentType, $charset]);

        header($header);
    }

    /**
     * Given a status code, returns a string with the code and status code description for use in the header
     * 
     * @param int $statusCode The status code we're wanting info on (not a complete list of status codes obviously)
     * 
     * @return string The status code and description
     */
    private function _getStatusCodeText(int $statusCode): string {
        switch ($statusCode) {
            case 200:
                return '200 OK';
            case 201:
                return '201 Created';
            case 203:
                return '203 Non-Authoritative Information';
            case 401:
                return '401 Unauthorized';
            case 404:
                return '404 Not Found';
            default:
                return '500 Internal Server Error';
        }
    }
}
