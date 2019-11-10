<?php

include "vendor/autoload.php";


$fluent = new \Sheriff\Elastic\FluentBuilder();

$fluent->query()
    ->bool()
    ->must(
        \Sheriff\Elastic\FluentBuilder::match()
        ->name([
            'query' => 'test'
        ]),

        \Sheriff\Elastic\FluentBuilder::matchAll()
            ->name([
                'query' => 'deneme'
            ])
    );

echo json_encode($fluent->build());