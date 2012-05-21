<?php

class CAS_Gallery_Session_Adapter implements CAS_Session_SessionHandler
{
    private $session = null;


    function hasSession()
    {
        return $this->session !== null;
    }

    function id()
    {
        if ($this->session === null)
            return '';
        else
            return $this->session->id();
    }

    function open()
    {
        if ($this->session === null)
            $this->session = Session::instance();
    }

    function start($id = null)
    {
        if ($id !== null)
            $id = $this->_sanatizeId($id);

        if ($this->session !== null)
            $this->session->create(null, $id);
        else
            $this->session = Session::instance($id);
    }

    function rename($id)
    {
        phpCAS :: trace("Skipping session rename gallery3 adapter does not support it.");
    }

    function destroy()
    {
        $this->open();
        $this->session->destroy();
    }

    private function _sanatizeId($id)
    {
        return preg_replace('/[^a-zA-Z0-9\-]/', '', $id);
    }
}
