<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 3/3/2016
 * Time: 3:14 PM
 */

namespace Acme\Mail;


interface ContactMailInterface
{
    public function setFrom($from);

    public function setFromName($fromName);

    public function setTo($to);

    public function setToName($toName);

    public function setSubject($subject);

    public function setMessage($message);

    public function getFrom();

    public function getFromName();

    public function getTo();

    public function getToName();

    public function getSubject();

    public function getMessage();
}