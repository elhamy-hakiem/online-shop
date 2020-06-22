<?php

function lang($phrase)
{
    static $lang = array(

       //Dashboard Links
       'HOME_ADMIN'   =>    'Dashboard',

       //navbar phrases
       'HOME'            =>    'HOME',
       'CATEGORIES'      =>    'Categories',
       'ITEMS'           =>    'Items',
       'MEMBERS'         =>    'Members',
       'COMMENTS'        =>    'Comments',
       'STATISTICS'      =>    'Statistics',
       'LOGS'            =>    'Logs',


       //user phrases
       'Edit_Profile'   =>     'Edit Profile',
       'Settings'        =>     'Settings',
       'Logout'          =>     'Logout',

    );
    return $lang[$phrase];
}


?>