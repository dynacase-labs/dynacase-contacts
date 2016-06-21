<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
*/
/**
 * Freedom Address Book
 *
 * @author Anakeen 2000
 * @version $Id: faddbook_main.php,v 1.27 2008/08/14 09:59:14 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 * @subpackage USERCARD
 */
/**
 */

include_once ("FDL/freedom_util.php");
include_once ("FDL/Lib.Dir.php");
/**
 * View list of document for a same family
 * @param Action &$action current action
 * @global string $chgAttr Http var :
 * @global string $chgId Http var :
 * @global string $chgValue Http var :
 * @global string $usedefaultview Http var : (Y|N) set Y if detail doc must be displayed with default view
 * @global string $etarget Http var : window target when edit doc
 * @global string $target Http var : window target when view doc
 * @global string $dirid Http var : folder/search id to restric searches
 * @global string $cols Http var : attributes id for column like : us_fname|us_lname
 * @global string $viewone Http var : (Y|N) set Y if want display detail doc if only one found
 * @global string $createsubfam Http var : (Y|N) set N if no want view possibility to create subfamily
 */
function faddbook_main(Action & $action)
{
    global $_POST;
    
    $rqi_form = array();
    foreach ($_POST as $k => $v) {
        if (substr($k, 0, 4) == "rqi_") $rqi_form[substr($k, 4) ] = $v;
    }
    
    $dbaccess = $action->dbaccess;
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/subwindow.js");
    
    $pstart = (int)GetHttpVars("sp", 0);
    $action->lay->set("choosecolumn", ($action->Haspermission("USERCARD_MANAGER", "USERCARD") == 1 ? true : false));
    $chattr = GetHttpVars("chgAttr", "");
    $chid = GetHttpVars("chgId", "");
    $chval = GetHttpVars("chgValue", "");
    $usedefaultview = (GetHttpVars("usedefaultview", "N") == "Y");
    $viewone = (GetHttpVars("viewone", "N") == "Y");
    $createsubfam = (GetHttpVars("createsubfam", "N") == "Y");
    $etarget = GetHttpVars("etarget");
    $target = GetHttpVars("target", "bookinfo");
    $dirid = GetHttpVars("dirid"); // restrict search
    $cols = GetHttpVars("cols"); // specific cols
    if ($chattr != "" && $chid != "") {
        $mdoc = new_Doc($dbaccess, $chid);
        $mdoc->setValue($chattr, $chval);
        $err = $mdoc->Modify();
        if ($err == "") AddWarningMsg($mdoc->title . " modifié (" . $mdoc->getAttribute($chattr)->getLabel() . " : " . $chval . ")");
    }
    $action->lay->set("viewpref", ($cols == ""));
    // Init page lines
    $lpage = $action->getParam("FADDBOOK_MAINLINE", 25);
    $action->lay->eSet("linep", $lpage);
    $choicel = array(
        10,
        25,
        50
    );
    $tl = array();
    foreach ($choicel as $v) {
        $tl[] = array(
            "count" => $v,
            "init" => ($lpage == $v ? "selected" : "")
        );
    }
    $action->lay->setBlockData("BLine", $tl);
    // propagate HTTP vars parameters
    $action->lay->eSet("sp", $pstart);
    $action->lay->eSet("lp", $lpage);
    $action->lay->eSet("target", $target);
    $action->lay->eSet("dirid", $dirid);
    $action->lay->eSet("etarget", $etarget);
    $action->lay->eSet("createsubfam", GetHttpVars("createsubfam"));
    $action->lay->eSet("usedefaultview", GetHttpVars("usedefaultview"));
    $action->lay->eSet("viewone", GetHttpVars("viewone"));
    $action->lay->eSet("cols", GetHttpVars("cols"));

    $sfullsearch = (GetHttpVars("sfullsearch", "") == "on" ? true : false);
    $action->lay->set("fullsearch", (bool)$sfullsearch);
    
    $sfam = GetHttpVars("dfam", $action->getParam("USERCARD_FIRSTFAM"));
    $action->lay->eSet("dfam", $sfam);
    $dnfam = new_Doc($dbaccess, $sfam);
    $action->lay->eSet("famid", $dnfam->id);
    $action->lay->eSet("famsearch", mb_convert_case(mb_strtolower($dnfam->title) , MB_CASE_TITLE));
    $dfam = createDoc($dbaccess, $sfam, false);
    $fattr = $dfam->GetAttributes();
    // Get user configuration
    $ucols = array();
    if ($cols) {
        $tccols = explode("|", $cols);
        foreach ($tccols as $v) {
            /**
             * @var string $v
             */
            $ucols[$v] = 1;
        }
        $action->lay->set("choosecolumn", false); // don't see choose column
        
    } else {
        $pc = $action->getParam("FADDBOOK_MAINCOLS", "");
        if ($pc != "") {
            $tccols = explode("|", $pc);
            foreach ($tccols as $v) {
                if ($v == "") continue;
                $x = explode("%", $v);
                if ($sfam == $x[0]) $ucols[$x[1]] = 1;
            }
        }
        if (count($ucols) == 0) {
            // default abstract
            $la = $dnfam->getAbstractAttributes();
            foreach ($la as $v) {
                if (($v->mvisibility != 'H') && ($v->mvisibility != 'I')) $ucols[$v->id] = 1;
            }
        }
    }
    // add sub families for creation
    $child = array();
    if (($dnfam->control("create") == "") && ($dnfam->control("icreate") == "")) {
        $child[$dnfam->id] = array(
            "title" => $dnfam->title,
            "id" => $dnfam->id
        );
    } else $child = array();
    
    if ($createsubfam) {
        $child+= $dnfam->GetChildFam($dnfam->id, true);
        $action->lay->set("viewsubfam", count($child) > 1);
        $action->lay->eSetBlockData("NEW", $child);
        $fc = current($child);
        $action->lay->eSet("famid", $fc["id"]);
        $action->lay->eSet("famsearch", mb_convert_case(mb_strtolower($fc["title"]) , MB_CASE_TITLE));
    } else {
        $action->lay->set("viewsubfam", false);
    }
    
    $action->lay->set("cancreate", count($child) > 0);

    $cols = 0;
    
    $td = array();
    $sf = "";
    $s = new SearchDoc($action->dbaccess, $sfam);
    $clabel = mb_convert_case(mb_strtolower($dnfam->title) , MB_CASE_TITLE);
    if (isset($rqi_form["__ititle"]) && $rqi_form["__ititle"] != "" && $rqi_form["__ititle"] != $clabel) {
        if ($sfullsearch) $s->addFilter("title ~* '%s'", pg_escape_string($rqi_form["__ititle"]));
        else $s->addFilter("title ~* '^%s'", pg_escape_string($rqi_form["__ititle"]));
        $sf = $rqi_form["__ititle"];
    }
    $td[] = array(
        "ATTimage" => false,
        "ATTnormal" => true,
        "id" => "__ititle",
        "label" => ($sf == "" ? $clabel : "$sf") ,
        "filter" => ($sf == "" ? false : true) ,
        "firstCol" => false
    );
    $cols++;
    
    $vattr = array();
    /**
     * @var NormalAttribute $v
     */
    foreach ($fattr as $v) {
        if ($v->type != "menu" && $v->type != "frame") {
            if (isset($ucols[$v->id]) && $ucols[$v->id] == 1) {
                $sf = "";
                $clabel = mb_convert_case(mb_strtolower($v->getLabel()) , MB_CASE_TITLE);
                $vattr[] = $v;
                $attimage = $attnormal = false;
                switch ($v->type) {
                    case "image":
                        $attimage = true;
                        break;

                    default:
                        $attnormal = true;
                }
                if (isset($rqi_form[$v->id]) && $rqi_form[$v->id] != "" && $rqi_form[$v->id] != $clabel) {
                    
                    $s->addFilter($v->id . " ~* '%s'", $rqi_form[$v->id]);
                    $sf = $rqi_form[$v->id];
                }
                $td[] = array(
                    "ATTimage" => (bool)$attimage,
                    "ATTnormal" => (bool)$attnormal,
                    "id" => $v->id,
                    "label" => ($sf == "" ? $clabel : "$sf") ,
                    "filter" => (bool)($sf == "" ? false : true) ,
                    "firstCol" => false
                );
                $cols++;
            }
        }
        $action->lay->eSetBlockData("COLS", $td);
    }
    
    $cl = $rq = $s->search();
    $dline = array();
    $il = 0;
    
    $action->lay->eSet("idone", ($viewone && (count($cl) == 1)) ? $cl[0]["id"] : false);
    
    foreach ($cl as $v) {
        if ($il >= $lpage) continue;
        $dcol = array();
        $ddoc = getDocObject($dbaccess, $v);
        $attchange = ($ddoc->Control("edit") == "" ? true : false);
        $dcol[] = array(
            "ATTchange" => false,
            "ATTname" => "",
            "content" => mb_convert_case(mb_strtolower($v["title"]) , MB_CASE_TITLE) ,
            "ATTimage" => false,
            "ATTnormal" => true
        );
        foreach ($vattr as $va) {
            $attimage = $attnormal = false;
            switch ($va->type) {
                case "image":
                    $attimage = true;
                    break;

                default:
                    $attnormal = true;
            }
            $dcol[] = array(
                "ATTchange" => false,
                "content" => $ddoc->GetHtmlAttrValue($va->id, "faddbook_blanck", false) ,
                "cid" => $v["id"],
                "ATTimage" => $attimage,
                "ATTnormal" => $attnormal,
                "ATTname" => $va->id
            );
        }
        $action->lay->esetBlockData("C$il", $dcol);
        $dline[$il]["cid"] = $v["id"];
        $dline[$il]["canChange"] = $attchange;
        $dline[$il]["etarget"] = ($etarget) ? $etarget : "edit" . $v["id"];
        $dline[$il]["title"] = xml_entity_encode(mb_convert_case(mb_strtolower($v["title"]) , MB_CASE_TITLE));
        $dline[$il]["Line"] = $il;
        $dline[$il]["icop"] = $dnfam->GetIcon($v["icon"]);
        $il++;
    }
    $pzone = ((!$usedefaultview) && isset($ddoc) && isset($ddoc->faddbook_card)) ? $ddoc->faddbook_card : "";
    $action->lay->set("fabzone", $pzone);
    
    $action->lay->setBlockData("DLines", $dline);
    $action->lay->set("colspan", ($cols + 2));
    
    $action->lay->set("NextPage", false);
    $action->lay->set("PrevPage", false);
    if (count($cl) > $lpage) {
        $action->lay->set("NextPage", true);
        $action->lay->set("pnext", ($pstart + 1));
    }
    if ($pstart > 0) {
        $action->lay->set("PrevPage", true);
        $action->lay->set("sp", ($pstart - 1));
        $action->lay->set("pprev", ($pstart - 1));
    }
    $action->lay->set("dirtitle", "");
    if ($dirid > 0) {
        $fdoc = new_doc($dbaccess, $dirid);
        $action->lay->set("dirtitle", $fdoc->title);
    }
}
