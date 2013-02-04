<?php

$app_desc = array(
    "name" => "USERCARD", //Name
    "short_name" => N_("User Card"), //Short name
    "description" => N_("User Card Management"), //long description
    "access_free" => "N", //Access free ? (Y,N)
    "icon" => "usercard.png", //Icon
    "displayable" => "Y", //Should be displayed on an app list (Y,N)
    "with_frame" => "Y", //Use multiframe ? (Y,N)
    "childof" => "", //
    "iorder" => 100, // install order
    "tag" => "CORE" //
);


$app_acl = array(

    array(
        "name" => "USERCARD_MASTER",
        "description" => N_("Access To Import Contacts"),
        "group_default" => "N"),
    array(
        "name" => "USERCARD_MANAGER",
        "description" => N_("Access To Manage Address Book"),
        "group_default" => "N"),
    array(
        "name" => "USERCARD_READ",
        "description" => N_("Access To View Address Book"),
        "group_default" => "Y")
);

$action_desc = array(

    array(
        "name" => "USERCARD_VCARD",
        "short_name" => N_("uc view as vcard"),
        "acl" => "USERCARD_READ"
    ),
    array(
        "name" => "USERCARD_EDITIMPORTVCARD",
        "short_name" => N_("uc edit import vcard"),
        "acl" => "USERCARD_READ"
    ),
    array(
        "name" => "USERCARD_IMPORTVCARD",
        "short_name" => N_("import vcard"),
        "layout" => "usercard_importvcard.xml",
        "acl" => "USERCARD_READ"
    ),
    array(
        "name" => "USERCARD_TAB",
        "short_name" => N_("tab usercard"),
        "acl" => "USERCARD_READ"
    ),

    array(
        "name" => "USERCARD_ROOT",
        "short_name" => N_("usercard home page"),
        "acl" => "USERCARD_READ",
        "root" => "N"
    ),

    array(
        "name" => "FADDBOOK_CSS",
        "short_name" => N_("address book css"),
        "acl" => "USERCARD_READ",
        "root" => "N"
    ),
    array(
        "name" => "FADDBOOK_SETUPARAM",
        "short_name" => N_("address book set user param"),
        "acl" => "USERCARD_READ",
        "root" => "N"
    ),
    array(
        "name" => "FADDBOOK_MAINCOLS",
        "short_name" => N_("address book choose main view column"),
        "acl" => "USERCARD_MANAGER",
        "root" => "N"
    ),
    array(
        "name" => "FADDBOOK_FRAME",
        "short_name" => N_("address book frame page"),
        "acl" => "USERCARD_READ",
        "root" => "Y"
    ),
    array(
        "name" => "FADDBOOK_MAIN",
        "short_name" => N_("address book main page"),
        "acl" => "USERCARD_READ",
        "root" => "N"
    ),
    array(
        "name" => "FADDBOOK_SPEEDSEARCH",
        "short_name" => N_("address book speed search"),
        "acl" => "USERCARD_READ",
        "root" => "N"
    ),
    array(
        "name" => "FADDBOOK_PREFERED",
        "short_name" => N_("address book prefered contacts"),
        "acl" => "USERCARD_READ",
        "root" => "N"
    ),
    array(
        "name" => "FADDBOOK_ADDPREFERED",
        "short_name" => N_("address book add a prefered contacts"),
        "acl" => "USERCARD_READ",
        "root" => "N"
    ),
    array(
        "name" => "FADDBOOK_DELPREFERED",
        "short_name" => N_("address book add a prefered contacts"),
        "acl" => "USERCARD_READ",
        "root" => "N"
    )


);

?>
