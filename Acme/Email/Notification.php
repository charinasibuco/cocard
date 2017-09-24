<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 3/3/2016
 * Time: 3:15 PM
 */

namespace Acme\Mail;


class Notification implements NotificationInterface, EmailInterface
{
    const TEMPLATE = 'emails.notification';

    public function execute(){
        $data = $this;
        Mail::send(self::TEMPLATE, ['data' => $data], function ($m) use ($data) {
            $m->from($data->getFrom(), $data->getFromName());

            $m->to($data->getTo(), $data->getToName())->subject($data->getSubject());
        });
    }
}