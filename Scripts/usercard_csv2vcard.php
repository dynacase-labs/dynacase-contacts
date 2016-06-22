<?php
/*
 * @author Anakeen
 * @package FDL
*/
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000
 * @version $Id: usercard_csv2vcard.php,v 1.6 2008/08/14 09:59:14 eric Exp $
 * @package FDL
 * @subpackage
 */
/**
 */
// remove all tempory doc and orphelines values
include_once ("FDL/Class.Doc.php");
include_once ("FDL/Class.UsercardVcard.php");
global $action;

$usage = new ApiUsage();

$usage->setDefinitionText("Change usercard from csv to vcard");
$fimport = $usage->addRequiredParameter("ifile", "file to convert"); // file to convert
$fvcf = $usage->addOptionalParameter("ofile", "output file", null, "php://stdin"); // output file

$usage->verify();

$appl = new Application();
$appl->Set("USERCARD", $core);

$dbaccess = $appl->dbaccess;
if ($dbaccess == "") {
    print "Database not found ";
    exit;
}

$doc = new_Doc($dbaccess, getFamIdFromName($dbaccess, "USER"));

$lattr = $doc->GetNormalAttributes();
$format = "DOC;" . $doc->id . ";<special id>;<special dirid>; ";

foreach($lattr as $attr) {
    $format.= $attr->getLabel() . " ;";
}
//print_r( $lattr);;
$usercard = new UsercardVcard();

$fdoc = fopen($fimport, "r");

$deffam = $action->GetParam("DEFAULT_FAMILY", getFamIdFromName($dbaccess, "USER"));

$usercard->open($fvcf, "w");
while ($data = fgetcsv($fdoc, 1000, ";")) {
    $num = count($data);
    if ($data[0] != "DOC") continue;
    if ($data[1] != $deffam) continue;
    
    $attr = array();
    reset($data);
    //array_shift($data);array_shift($data);array_shift($data);array_shift($data);
    while (list($k, $v) = each($data)) {
        if ($k > 3) $attr[$lattr[$k - 4]->id] = $v;
    }
    
    $usercard->WriteCard($attr["us_lname"] . " " . $attr["us_fname"], $attr);
}
?>