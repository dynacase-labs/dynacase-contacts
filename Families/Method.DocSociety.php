<?php
/*
 * @author Anakeen
 * @package FDL
*/
/**
 * Methods for society document
 *
 * @author Anakeen 2000
 * @version $Id: Method.DocSociety.php,v 1.1 2004/01/15 10:12:02 eric Exp $
 * @package FDL
 * @subpackage USERCARD
 */
/**
 */
/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
class _SOCIETY extends Doc
{
    /*
     * @end-method-ignore
    */
    
    function UpdateSite()
    {
        
        include_once ("FDL/Lib.Dir.php");
        // contracts():SI_IDCONTRATS,SI_CONTRATS
        if ($this->initid > 0) {
            $filter[] = "si_idsoc =  '" . $this->initid . "'";
            $s = new SearchDoc($this->dbaccess, "SITE");
            $s->overrideViewControl();
            $s->setObjectReturn(false);
            $tsite = $s->search();
            $idc = array();
            $tc = array();
            foreach ($tsite as $k => $v) {
                $idc[] = $v["id"];
                $tc[] = $v["title"];
            }
            
            $this->setValue("SI_IDSITES", $idc);
            $this->setValue("SI_SITES", $tc);
        }
    }
    function preRefresh()
    {
        $this->UpdateSite();
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