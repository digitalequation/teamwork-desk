<?php

/* @var $factory Factory */

use DigitalEquation\TeamworkDesk\Models\SupportTicket;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(SupportTicket::class, function (Faker $faker) {
    return [
        'user_id'          => null,
        'ticket_id'        => rand(1, 100),
        'event_creator_id' => rand(1, 100),
    ];
});
