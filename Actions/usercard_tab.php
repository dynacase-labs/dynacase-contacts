<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
*/
/**
 * State document edition
 *
 * @author Anakeen 2000
 * @version $Id: usercard_tab.php,v 1.7 2005/05/19 14:38:44 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 * @subpackage
 */
/**
 */

include_once ("FDL/Class.WDoc.php");
include_once ("Class.QueryDb.php");
include_once ("FDL/freedom_util.php");
include_once ("FDL/editutil.php");
include_once ("FDL/editcard.php");
// -----------------------------------
function usercard_tab(Action & $action)
{
    $dbaccess = $action->GetParam("FREEDOM_DB");
    /**
     * @var DocSearch $sdoc
     */
    $sdoc = createDoc($dbaccess, 5, false); //new DocSearch($dbaccess);
    $sdoc->doctype = 'T'; // it is a temporary document (will be delete after)
    $sdoc->Add();
    
    $default = GetHttpVars("default", "Y");
    $fam = "USER";
    
    if ($default == "N") {
        $fam = GetHttpVars("family");
    }
    
    $famid = getFamIdFromName($dbaccess, $fam);
    // $sdoc->title=sprintf(_("%s"),getTitle($famid));
    $sdoc->title = _($fam);
    $sqlfilter[] = "(fromid=$famid)";
    //filters for USER or IUSER
    if (($fam == "USER" or $fam == "IUSER") and $default == "N") {
        //criteres
        $contact = GetHttpVars("contact");
        $Tcontact = explode(" ", $contact);
        $ch = "";
        foreach ($Tcontact as $k => $v) {
            if ($ch <> "") $ch.= " and ";
            $ch.= " title ~* '$Tcontact[$k]'";
        }
        if ($contact <> "") $sqlfilter[] = "$ch or us_mail ~* '$contact' ";
        
        $society = GetHttpVars("society");
        $Tsoc = explode(" ", $society);
        $ch = "";
        foreach ($Tsoc as $k => $v) {
            if ($ch <> "") $ch.= " and ";
            $ch.= " us_society ~* '$Tsoc[$k]'";
        }
        if ($society <> "") $sqlfilter[] = "$ch";
        
        $private = GetHttpVars("private");
        if ($private == 1) $sqlfilter[] = "us_privcard='P' ";
        //details
        $allcond = GetHttpVars("allcond");
        if ($allcond == 1) $op = "and";
        else $op = "or";
        
        $mail = GetHttpVars("mail");
        $phone = GetHttpVars("phone");
        $pphone = GetHttpVars("pphone");
        $mobile = GetHttpVars("mobile");
        $adr = GetHttpVars("adr");
        $postalcode = GetHttpVars("postalcode");
        $town = GetHttpVars("town");
        $country = GetHttpVars("country");
        $function = GetHttpVars("function");
        $catg = GetHttpVars("catg");
        
        $sql = array();
        if ($mail <> "")       $sql[] = sprintf(" us_mail           ~* '%s'", pg_escape_string($mail));
        if ($phone <> "")      $sql[] = sprintf(" us_phone          ~* '%s'", pg_escape_string($phone));
        if ($pphone <> "")     $sql[] = sprintf(" us_pphone         ~* '%s'", pg_escape_string($pphone));
        if ($mobile <> "")     $sql[] = sprintf(" us_mobile         ~* '%s'", pg_escape_string($mobile));
        if ($adr <> "")        $sql[] = sprintf(" us_workaddr       ~* '%s'", pg_escape_string($adr));
        if ($postalcode <> "") $sql[] = sprintf(" us_workpostalcode ~* '%s'", pg_escape_string($postalcode));
        if ($town <> "")       $sql[] = sprintf(" us_worktown       ~* '%s'", pg_escape_string($town));
        if ($country <> "")    $sql[] = sprintf(" us_country        ~* '%s'", pg_escape_string($country));
        if ($function <> "")   $sql[] = sprintf(" us_type           ~* '%s'", pg_escape_string($function));
        if ($catg <> "")       $sql[] = sprintf(" us_scatg          ~* '%s'", pg_escape_string($catg));
        
        $ch = "";
        foreach ($sql as $k => $v) {
            if ($ch <> "") $ch.= " $op ";
            $ch.= $sql[$k];
        }
        
        if ($ch <> "") $sqlfilter[] = $ch;
    }
    //filters for SOCIETY
    if ($fam == "SOCIETY" and $default == "N") {
        //criteres
        $society = GetHttpVars("society");
        $Tsoc = explode(" ", $society);
        $ch = "";
        foreach ($Tsoc as $k => $v) {
            if ($ch <> "") $ch.= " and ";
            $ch.= " si_society ~* '$Tsoc[$k]'";
        }
        if ($society <> "") $sqlfilter[] = "$ch";
        //details
        $allcond = GetHttpVars("allcond");
        if ($allcond == 1) $op = "and";
        else $op = "or";
        
        $phone = GetHttpVars("phone");
        $adr = GetHttpVars("adr");
        $catg = GetHttpVars("catg");
        $postalcode = GetHttpVars("postalcode");
        $town = GetHttpVars("town");
        $country = GetHttpVars("country");
        
        $sql = array();
        if ($phone <> "")      $sql[] = sprintf(" si_phone      ~* '%s'", pg_escape_string($phone));
        if ($adr <> "")        $sql[] = sprintf(" si_addr       ~* '%s'", pg_escape_string($adr));
        if ($postalcode <> "") $sql[] = sprintf(" si_postalcode ~* '%s'", pg_escape_string($postalcode));
        if ($town <> "")       $sql[] = sprintf(" si_town       ~* '%s'", pg_escape_string($town));
        if ($country <> "")    $sql[] = sprintf(" si_country    ~* '%s'", pg_escape_string($country));
        if ($catg <> "")       $sql[] = sprintf(" si_catg       ~* '%s'", pg_escape_string($catg));
        
        $ch = "";
        foreach ($sql as $k => $v) {
            if ($ch <> "") $ch.= " $op ";
            $ch.= $sql[$k];
        }
        
        if ($ch <> "") $sqlfilter[] = $ch;
    }
    //REQUETE
    $sdirid = 0;
    $query = getSqlSearchDoc($dbaccess, $sdirid, $famid, $sqlfilter);
    
    $sdoc->AddQuery($query);
    redirect($action, "FREEDOM", "FREEDOM_LISTDETAIL&dirid=" . $sdoc->id . "&catg=0");
}
