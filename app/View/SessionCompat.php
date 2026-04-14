<?php

namespace App\View;

/**
 * CI3-compatible wrapper around CI4's Session.
 *
 * Maps old CI3 method names (userdata, set_userdata, flashdata, etc.)
 * to their CI4 equivalents and passes everything else through.
 */
class SessionCompat
{
    private $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    // ---- CI3 method names --------------------------------------------------

    /**
     * CI3: $this->session->userdata('key')  or  $this->session->userdata()
     * @deprecated Use session()->get($key) instead
     */
    public function userdata($key = null)
    {
        log_message('warning', 'DEPRECATED: $this->session->userdata() - Use session()->get() instead');
        if ($key === null) {
            return $this->session->get();
        }
        return $this->session->get($key);
    }

    /**
     * CI3: $this->session->set_userdata('key', 'value')
     *       $this->session->set_userdata(['key' => 'value'])
     */
    public function set_userdata($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->session->set($k, $v);
            }
        } else {
            $this->session->set($key, $value);
        }
    }

    /**
     * CI3: $this->session->unset_userdata('key')
     */
    public function unset_userdata($key)
    {
        $this->session->remove($key);
    }

    /**
     * CI3: $this->session->has_userdata('key')
     */
    public function has_userdata($key): bool
    {
        return $this->session->has($key);
    }

    /**
     * CI3: $this->session->flashdata('key')
     */
    public function flashdata($key = null)
    {
        if ($key === null) {
            return $this->session->getFlashdata();
        }
        return $this->session->getFlashdata($key);
    }

    /**
     * CI3: $this->session->set_flashdata('key', 'value')
     */
    public function set_flashdata($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->session->setFlashdata($k, $v);
            }
        } else {
            $this->session->setFlashdata($key, $value);
        }
    }

    /**
     * CI3: $this->session->tempdata('key')
     */
    public function tempdata($key = null)
    {
        if ($key === null) {
            return $this->session->getTempdata();
        }
        return $this->session->getTempdata($key);
    }

    /**
     * CI3: $this->session->set_tempdata('key', 'value', $ttl)
     */
    public function set_tempdata($key, $value = null, $ttl = 300)
    {
        $this->session->setTempdata($key, $value, $ttl);
    }

    /**
     * CI3: $this->session->unset_tempdata('key')
     */
    public function unset_tempdata($key)
    {
        $this->session->removeTempdata($key);
    }

    /**
     * CI3: $this->session->unset_session() - destroys the entire session
     */
    public function unset_session()
    {
        $this->session->destroy();
    }

    public function stop()
    {
        $this->session->destroy();
    }

    // ---- Pass-through for CI4 methods and any other calls ------------------

    public function __call($method, $args)
    {
        return $this->session->$method(...$args);
    }

    public function __get($name)
    {
        return $this->session->$name;
    }

    public function __set($name, $value)
    {
        $this->session->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->session->$name);
    }
}
