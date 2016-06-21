<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
*/
/**
 * Generate bar menu
 *
 * @author Anakeen
 * @version $Id: faddbook_menu.php,v 1.4 2008/08/14 09:59:14 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 * @subpackage
 */
/**
 */

include_once ("GENERIC/generic_util.php");
// -----------------------------------
function faddbook_menu(Action & $action)
{
    // -----------------------------------
    global $dbaccess; // use in getChildCatg function
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/AnchorPosition.js");
    
    $famid = GetHttpVars("dfamid");
    $onemenu = false; //display menu
    if ($action->Read("navigator", "") == "EXPLORER") {
        // special for position style
        $action->lay->set("positionstyle", "");
        $action->lay->set("fhelp", "_blank");
    } else {
        $action->lay->set("positionstyle", "fixed");
        $action->lay->set("fhelp", "fhidden");
    }
    
    $dbaccess = $action->dbaccess;
    
    $fdoc = new_Doc($dbaccess, $famid);
    /**
     * @var DocFam $fdoc
     */
    $action->lay->eSet("famid", $famid);
    $action->lay->Set("topid", $fdoc->dfldid);
    
    include_once ("FDL/popup_util.php");
    //--------------------- construction of  menu -----------------------
    popupInit("helpmenu", array(
        'impcsv',
        'folders'
    ));
    
    $lmenu = $fdoc->GetMenuAttributes();
    $tmenu = array();
    foreach ($lmenu as $k => $v) {
        if ($v->getOption("global") == "yes") {
            $confirm = ($v->getOption("lconfirm") == "yes");
            $tmenu[$k] = array(
                "mid" => $v->id,
                "mtarget" => ($v->getOption("ltarget") != "") ? $v->getOption("ltarget") : $v->id,
                "mtitle" => $v->getLabel(),
                "confirm" => ($confirm) ? "true" : "false",
                "tconfirm" => ($confirm) ? sprintf(_("uc Sure %s ?") , addslashes($v->getLabel())) : "",
                "murl" => addslashes($fdoc->urlWhatEncode($v->link))
            );
            
            popupAddItem('helpmenu', $v->id);
            $vis = MENU_ACTIVE;
            if ($v->precond != "") $vis = $fdoc->ApplyMethod($v->precond, MENU_ACTIVE);
            if ($vis == MENU_ACTIVE) {
                $onemenu = true;
                popupActive("helpmenu", 1, $v->id);
            }
        }
    }
    
    $action->lay->eSetBlockData("FAMMENU", $tmenu);
    
    if ($action->HasPermission("GENERIC_MASTER", "GENERIC")) {
        popupActive("helpmenu", 1, 'impcsv');
        $onemenu = true;
    } else {
        popupInvisible("helpmenu", 1, 'impcsv');
    }
    
    popupInvisible("helpmenu", 1, 'folders');
    if ($action->HasPermission("FREEDOM_GED", "FREEDOM")) {
        popupActive("helpmenu", 1, 'folders');
        $onemenu = true;
    }
    
    popupGen(1);
    
    $action->lay->set("ONEMENU", $onemenu);
}
