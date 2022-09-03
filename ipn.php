<?php
include_once 'controllers/paymentController.php';
$req=(object)$_REQUEST;
(new paymentController())->callback($req);
