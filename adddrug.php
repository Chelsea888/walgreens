<?php
require "header.php";

use Cart\CartItem;

$item = new CartItem;
$item->name = 'Macbook Pro';
$item->sku = 'MBP8GB';
$item->price = 1200;
$item->tax = 200;

$cart->add($item);

$cart->save();
