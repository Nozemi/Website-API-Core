<?php

    $url = 'https://services.runescape.com/m=itemdb_oldschool/obj_big.gif?id=';
    $img = './img/items/';

    for($i = 0; $i < 100; $i++) {
        file_put_contents($img . $i . '.gif', file_get_contents($url . $i));
    }