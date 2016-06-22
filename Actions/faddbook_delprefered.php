<?php
/*
 * @author Anakeen
 * @package FDL
*/
/**
 * Delete Prefered persons
 *
 * @author Anakeen 2005
 * @version $Id: faddbook_delprefered.php,v 1.4 2005/11/24 13:48:17 eric Exp $
 * @package FDL
 * @subpackage USERCARD
 */
/**
 */
function faddbook_delprefered(Action & $action)
{
    
    $cid = GetHttpVars("cid", 0);
    if ($cid < 0 && $cid != - 2) return;
    if ($cid == - 2) {
        $stc = "";
    } else {
        $cpref = $action->getParam("FADDBOOK_PREFERED", "");
        $tc = explode("|", $cpref);
        $ntc = array();
        foreach ($tc as $k => $v) if ($v != $cid) $ntc[] = $v;
        $stc = implode("|", $ntc);
    }
    $action->parent->param->set("FADDBOOK_PREFERED", $stc, PARAM_USER . $action->user->id, $action->parent->id);
    Redirect($action, "USERCARD", "FADDBOOK_PREFERED");
}
