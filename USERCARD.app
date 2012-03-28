<?php

$app_desc = array(
    "name" => "USERCARD",
    //Name
    "short_name" => N_("USERCARD name"),
    //Short name
    "description" => N_("USERCARD description"),
    //long description
    "access_free" => "N",
    //Access free ? (Y,N)
    "icon" => "USERCARD.png",
    //Icon
    "displayable" => "Y",
    //Should be displayed on an app list (Y,N)
    "with_frame" => "Y",
    //Use multiframe ? (Y,N)
    "childof" => "" // instance of other application
);

/*
//Example for construct application acl 
$app_acl = array(
    array(
        "name" => "USERCARD_ACLONE",
        "description" => N_("Access to ticket sales")
    )
);

// Example for describe action
$action_desc = array(
    array(
        "name" => "USERCARD_TICKETSALES",
        "short_name" => N_("sum of sales"),
        "acl" => "USERCARD_ACLONE"
    ),
    array(
        "name" => "USERCARD_TEXTTICKETSALES",
        "short_name" => N_("text sum of sales"),
        "script" => "zoo_ticketsales.php",
        "function" => "zoo_ticketsales",
        "acl" => "USERCARD_ACLONE"
    )
)
*/

?>
