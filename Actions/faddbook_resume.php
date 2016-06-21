<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
*/
/**
 * Display thumb person card
 *
 * @author Anakeen 2005
 * @version $Id: faddbook_resume.php,v 1.2 2005/11/24 13:48:17 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 * @subpackage USERCARD
 */
/**
 */
include_once ("FDL/freedom_util.php");

function faddbook_resume(Action & $action)
{
    echo "totoqsdqsmlkdjqsmldqsjdmlqsjmdl";
    $id = GetHttpVars("id", "");
    if ($id == "") {
        echo "pas did";
        return;
    }
    $db = $action->dbaccess;
    
    $ct = new_Doc($db, $id);
    if (!$ct->isAffected()) {
        echo "pas de doc";
        return;
    }
    
    if ($ct->getRawValue("us_photo") == "") $photo = $action->parent->GetImagelink("faddbook_nophoto.gif");
    else $photo = $ct->getIcon($ct->getRawValue("us_photo") == "");
    $action->lay->set("photo", $photo);
    
    $civ = $ct->getRawValue("us_civility");
    $action->lay->set("hasCiv", ($civ != "" ? true : false));
    $action->lay->eSet("civilite", $civ);
    
    $action->lay->eSet("prenom", $ct->getRawValue("us_lname"));
    $action->lay->eSet("nom", $ct->getRawValue("us_fname"));
    
    $mail = $ct->getRawValue("us_mail");
    $action->lay->set("hasMail", ($mail != "" ? true : false));
    $action->lay->eSet("addmail", $mail);
    
    $action->lay->eSet("nomob", $ct->getRawValue("us_mobile"));
    $action->lay->eSet("notel", $ct->getRawValue("us_phone"));
    
    $action->lay->set("skypeid", "");
    $action->lay->set("msnid", "");
    
    return;
}
