<?php
if (! empty($msg['thankyou'])) print $msg['thankyou'];

showPurchasedProducts($db, $cart_id);