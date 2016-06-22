<?php
/*
 * @author Anakeen
 * @package FDL
*/
/**
 * Adsress book methods for persons
 *
 * @author Anakeen 2005
 * @version $Id: Method.FAddBook.php,v 1.15 2008/05/13 10:21:01 eric Exp $
 * @package FDL
 * @subpackage USERCARD
 */
/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
class _USERBOOK extends Doc
{
    /*
     * @end-method-ignore
    */
    public $faddbook_card = "USERCARD:VIEWPERSON:T";
    public $faddbook_resume = "USERCARD:FADDBOOK_RESUME:U";
    /**
     * @templateController
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     * @return mixed
     */
    function faddbook_resume($target = "finfo", $ulink = true, $abstract = false)
    {
        
        global $action;
        $action->parent->AddCssRef("USERCARD:faddbook.css", true);
        
        $imgu = "";
        $img = $this->getRawValue("us_photo");
        if ($img == "") {
            $this->lay->set("hasPhoto", false);
        } else {
            $this->lay->set("hasPhoto", true);
            $imgu = $this->GetHtmlValue($this->getAttribute("us_photo") , $img);
            $this->lay->set("photo", $imgu);
        }
        
        $this->lay->eset("nom", $this->getRawValue("us_lname"));
        $this->lay->eset("prenom", $this->getRawValue("us_fname"));
        
        $soc = $this->getRawValue("us_society");
        $this->lay->set("hasSoc", ($soc != "" ? true : false));
        $this->lay->eset("societe", $soc);
        
        $mail = $this->getRawValue("us_mail");
        $this->lay->set("hasMail", ($mail != "" ? true : false));
        $this->lay->set("addmail", $mail);
        
        $mob = $this->getRawValue("us_mobile");
        $this->lay->eset("nomob", $mob);
        $this->lay->set("hasMob", ($mob != "" ? true : false));
        
        $tel = $this->getRawValue("us_phone");
        $this->lay->eset("notel", $tel);
        $this->lay->set("hasTel", ($tel != "" ? true : false));
        
        $sky = $this->getRawValue("us_skypeid");
        $this->lay->eset("skypeid", $sky);
        $this->lay->set("hasSky", ($sky != "" ? true : false));
        
        $msn = $this->getRawValue("us_msnid");
        $this->lay->eset("msnid", $msn);
        $this->lay->set("hasMsn", ($msn != "" ? true : false));
        
        return;
    }
    /**
     * @templateController
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     */
    function faddbook_card($target = "finfo", $ulink = true, $abstract = false)
    {
        // list of attributes displayed directly in layout
        global $action;
        $action->parent->AddCssRef("USERCARD:faddbook.css", true);
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/USERCARD/Layout/faddbook.js");
        
        setHttpVar("specialmenu", "menuab");
        
        $ta = array(
            "us_workweb",
            "us_photo",
            "us_lname",
            "us_fname",
            "us_society",
            "us_civility",
            "us_mail",
            "us_phone",
            "us_mobile",
            "us_fax",
            "us_intphone",
            "us_workaddr",
            "us_workcedex",
            "us_country",
            "us_workpostalcode",
            "us_worktown"
        );
        
        $this->viewdefaultcard($target, $ulink, $abstract);
        $la = $this->getAttributes();
        $to = array();
        $tabs = array();
        foreach ($la as $k => $v) {
            $va = $this->getRawValue($v->id);
            if (($va || ($v->type == "array")) && (!in_array($v->id, $ta)) && (!$v->inArray())) {
                
                if ((($v->mvisibility == "R") || ($v->mvisibility == "W"))) {
                    if ($v->type == "array") {
                        $hv = $this->getHtmlValue($v, $va, $target, $ulink);
                        if ($hv) {
                            $to[] = array(
                                "lothers" => $v->labelText,
                                "aid" => $v->id,
                                "vothers" => $hv,
                                "isarray" => true
                            );
                            $tabs[$v->fieldSet->labelText][] = $v->id;
                        }
                    } else {
                        $to[] = array(
                            "lothers" => $v->labelText,
                            "aid" => $v->id,
                            "vothers" => $this->getHtmlValue($v, $va, $target, $ulink) ,
                            "isarray" => false
                        );
                        $tabs[$v->fieldSet->labelText][] = $v->id;
                    }
                }
            }
        }
        $this->lay->setBlockData("OTHERS", $to);
        $ltabs = array();
        foreach ($tabs as $k => $v) {
            $ltabs[$k] = array(
                "tabtitle" => $k,
                "aids" => "['" . implode("','", $v) . "']"
            );
        }
        $this->lay->setBlockData("TABS", $ltabs);
    }
    /**
     * @templateController
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     */
    function viewperson($target = "finfo", $ulink = true, $abstract = false)
    {
        $this->viewdefaultcard($target, $ulink, $abstract);
        $socid = $this->getRawValue("us_idsociety");
        $soc = null;
        if ($socid) $soc = new_doc($this->dbaccess, $socid);
        if ($socid && $soc->isAlive()) {
            $this->lay->set("socphone", $soc->getRawValue("si_phone"));
            $this->lay->set("socfax", $soc->getRawValue("si_fax"));
        } else {
            $this->lay->set("socphone", "");
            $this->lay->set("socfax", "");
        }
        $secid = $this->getRawValue("us_idsecr");
        $sec = null;
        if ($secid) $sec = new_doc($this->dbaccess, $secid);
        if ($secid && $sec->isAlive()) {
            $this->lay->set("secrphone", $sec->getRawValue("us_pphone"));
        } else {
            $this->lay->set("secrphone", "");
        }
    }
    /**
     * @begin-method-ignore
     * this part will be deleted when construct document class until end-method-ignore
     */
}
/*
 * @end-method-ignore
*/
?>
