<?php defined("SYSPATH") or die("No direct script access.");

/**
 * Copyright (c) 2012 infinite Group Ltd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class CAS_Gallery_Session_Adapter implements CAS_Session_SessionHandler
{
  private $session = null;


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

  function openSpecificSession($ticket)
  {
    $ticket = $this->_sanatizeId($ticket);
    $this->session = Session::instance($ticket);
  }

  function rename($ticket)
  {
    $id = $this->_sanatizeId($ticket);
    $oldSession = $_SESSION;

    $this->open();
    $this->session->create(null, $id);

    $_SESSION = $oldSession;
    $_SESSION["session_id"] = $id;
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
