<?php
namespace Framework\Session;

interface SessionInterface
{


    /**
     * Get an information in Session
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);


    /**
     * Set an information in Session
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value): void;


    /**
     * Delete a key in Session
     *
     * @param string $key
     */
    public function delete(string $key): void;
}
