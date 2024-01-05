<?php

namespace DBarbieri\Graylog;

use Exception;

class Graylog
{

    const LEVEL_FATAL = "Fatal";
    const LEVEL_ERROR = "Error";
    const LEVEL_WARNING = "Warn";
    const LEVEL_INFO = "Info";
    const LEVEL_DEBUG = "Debug";
    const LEVEL_TRACE = "Trace";

    private string $host = "";
    private int $port = 80;
    private array $content = [];

    public function __construct(string $host, int $port = 80)
    {
        if (substr_count($host, ':') > 1) {
            $port = explode(":", $host)[2];
            $host = str_replace(":" . $port, "", $host);
        }
        $this->setHost($host);
        $this->setPort($port);
    }

    public function send(?array $content = null)
    {
        $content ? $content = $this->content = $content : null;

        if (count($this->getContent()) < 1) {
            throw new Exception("Graylog content is empty!");
        }

        $url = $this->getHost() . ":" . $this->getPort() . "/gelf";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getJsonContent());
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        $return = (object) array(
            "code" => $code,
            "return" => $output,
            "error" => $error
        );

        curl_close($ch);

        if ($code == 202) {
            return true;
        }

        throw new Exception($error);
    }



    /**
     * Get the value of host
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Set the value of host
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the value of port
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Set the value of port
     */
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the value of content
     */
    public function getContent(): array
    {
        return $this->content;
    }

    public function getJsonContent(): string
    {
        return json_encode($this->getContent());
    }

    /**
     * Set the value of content
     */
    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }
}
