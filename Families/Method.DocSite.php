<?php
/*
 * @author Anakeen
 * @package FDL
*/
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000
 * @version $Id: Method.DocSite.php,v 1.5 2005/06/28 08:37:46 eric Exp $
 * @package FDL
 * @subpackage USERCARD
 */
/**
 */
/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
class _SITE extends Doc
{
    /*
     * @end-method-ignore
    */
    
    function postStore()
    {
        // refresh mother society
        $ids = $this->getRawValue("SI_IDSOC");
        $soc = new_Doc($this->dbaccess, $ids);
        if ($soc->isAlive()) $soc->refresh();
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