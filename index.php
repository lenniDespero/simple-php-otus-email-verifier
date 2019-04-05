<?php

require './vendor/autoload.php';

use Otus\Lessons\Lesson7\Verifier;

$someMails  = [
    '12345@mail.ru',
    'example@example.com',
    '12345@yandex.ru'
];

$verifier = new Verifier();

$verifier->addMails($someMails);
//var_dump($verifier);
$verifier->removeMails('example@example.com');
//var_dump($verifier);
$verifier->addMails('example@example.com');
$verifier->addMails('example@example.com');
//var_dump($verifier->getMails());
$verifier->verify();