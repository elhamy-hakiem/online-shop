<?php

function lang($phrase)
{
    static $lang = array(

        'Message' => 'مرحبا',
        'Admin' => 'مدير'

    );
    return $lang[$phrase];
}


?>