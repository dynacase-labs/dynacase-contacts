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
    $db = $action->getParam("FREEDOM_DB");
    
    $ct = new_Doc($db, $id);
    if (!$ct->isAffected()) {
        echo "pas de doc";
        return;
    }
    
    if ($ct->getValue("us_photo") == "") $photo = $action->GetImageUrl("faddbook_nophoto.gif");
    else $photo = $ct->getIcon($ct->getValue("us_photo") == "");
    $action->lay->set("photo", $photo);
    
    $civ = $ct->getValue("us_civility");
    $action->lay->set("hasCiv", ($civ != "" ? true : false));
    $action->lay->eSet("civilite", $civ);
    
    $action->lay->eSet("prenom", $ct->getValue("us_lname"));
    $action->lay->eSet("nom", $ct->getValue("us_fname"));
    
    $mail = $ct->getValue("us_mail");
    $action->lay->set("hasMail", ($mail != "" ? true : false));
    $action->lay->eSet("addmail", $mail);
    
    $action->lay->eSet("nomob", $ct->getValue("us_mobile"));
    $action->lay->eSet("notel", $ct->getValue("us_phone"));
    
    $action->lay->set("skypeid", "");
    $action->lay->set("msnid", "");
    
    return;
}
