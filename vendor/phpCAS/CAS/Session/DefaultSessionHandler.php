<?php

/**
 * Licensed to Jasig under one or more contributor license
 * agreements. See the NOTICE file distributed with this work for
 * additional information regarding copyright ownership.
 *
 * Jasig licenses this file to you under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP Version 5
 *
 * @file     CAS/Session/DefaultSessionHandler.php
 * @category Authentication
 * @package  PhpCAS
 * @author   Chris Chilvers <chilversc@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASC/phpCAS
 */

class CAS_Session_DefaultSessionHandler implements CAS_Session_SessionHandler
{
    function id()
    {
        return session_id();
    }

    function open()
    {
        if (session_id() === '')
            session_start();
    }

    function openSpecificSession($ticket)
    {
        $this->destroy();

        $ticket = $this->_sanatizeId($ticket);
        session_id($ticket);
        $_COOKIE[session_name()] = $ticket;
        $_GET[session_name()] = $ticket;

        session_start();
    }

    function rename($ticket)
    {
        $old_session = $_SESSION;
        $this->destroy();
        // set up a new session, of name based on the ticket
        $this->openSpecificSession($ticket);
        $session_id = $this->id();
        phpCAS :: trace("Session ID: ".$session_id);
        phpCAS :: trace("Restoring old session vars");
        $_SESSION = $old_session;
        $_COOKIE[session_name()] = $ticket;
        $_GET[session_name()] = $ticket;
    }

    function destroy()
    {
        if (session_id() !== '') {
            session_unset();
            session_destroy();
            $_SESSION = array();
        }
   }

    private function _sanatizeId($id)
    {
        return preg_replace('/[^a-zA-Z0-9\-]/', '', $id);
    }
}
