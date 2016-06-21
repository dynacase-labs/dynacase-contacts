<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
*/
/**
 * Initiate LDAP database
 *
 * @author Anakeen
 * @version $Id: usercard_ldapinit.php,v 1.18 2007/03/26 14:09:35 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 * @subpackage
 */
/**
 */
// remove all tempory doc and orphelines values
include_once ("FDL/Class.Doc.php");
include_once ("FDL/Lib.Dir.php");

define("REDCOLOR", "\033" . '[1;31;40m');
define("UPDTCOLOR", "\033" . '[1;32;40m');
define("SKIPCOLOR", "\033" . '[1;33;40m');
define("STOPCOLOR", "\033" . '[0m');
global $action;

$usage = new ApiUsage();

$usage->setDefinitionText("Initiate LDAP database");
$clean = ($usage->addOptionalParameter("clean", "clean database option", array("yes", "no"), "no") == "yes"); // clean databases option

$usage->verify();

$appl = new Application();
$appl->Set("USERCARD", $core);

if ($action->GetParam("LDAP_ENABLED", "no") != "yes") {
    $err = "LDAP disabled : do nothing ; modify LDAP_ENABLED parameter if you want update LDAP usercard";
    print $err;
    wbar(0, 0, $err);
    return true;
}
$dbaccess = $appl->dbaccess;
if ($dbaccess == "") {
    $err = "Database not found ";
    print $err;
    wbar(0, 0, $err);
    return true;
}

$ldaphost = $action->GetParam("LDAP_SERVEUR", "localhost");
$ldappw = $action->GetParam("LDAP_ROOTPW");
$ldapdn = $action->GetParam("LDAP_ROOTDN");
$ldapr = $action->GetParam("LDAP_ROOT");
if ($clean) {
    $msg = sprintf(_("delete %s on server %s...\n") , $ldapr, $ldaphost);
    print $msg;
    wbar(1, -1, $msg);
    system(sprintf("ldapdelete -r -h %s -D %s -x -w %s %s", escapeshellarg($ldaphost) , escapeshellarg($ldapdn) , escapeshellarg($ldappw) , escapeshellarg($ldapr)));
    wbar(1, -1, _("LDAP cleaned"));
}

$ldoc1 = internalGetDocCollection($dbaccess, 0, 0, "ALL", array() , $action->user->id, "ITEM", "USER");
$ldoc2 = internalGetDocCollection($dbaccess, 0, 0, "ALL", array() , $action->user->id, "ITEM", "IGROUP");
$ldoc = array_merge($ldoc1, $ldoc2);
$reste = countDocs($ldoc);
$total = $reste;
reset($ldoc);
while ($doc = getNextDoc($dbaccess, $ldoc)) {
    //print $doc->title."\n";
    // update LDAP only no private card
    $err = $doc->RefreshLdapCard();
    if (($err == "") && ($err !== false)) print UPDTCOLOR . $reste . ")" . $doc->title . ": updated" . STOPCOLOR . "\n";
    else print SKIPCOLOR . $reste . ")" . $doc->title . ": skipped : $err" . STOPCOLOR . "\n";
    $reste--;
    
    wbar($reste, $total);
}

if ($fbar) {
    unlink($fbar);
}
?>
