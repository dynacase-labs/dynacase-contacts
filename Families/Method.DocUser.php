<?php
/*
 * @author Anakeen
 * @package FDL
*/
/**
 * Persons & LDAP methods
 *
 * @author Anakeen 2000
 * @version $Id: Method.DocUser.php,v 1.37 2006/06/23 15:30:55 eric Exp $
 * @package FDL
 * @subpackage USERCARD
 */
/**
 */
/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
class _USER extends Doc implements IMailRecipient
{
    /*
     * @end-method-ignore
    */
    
    var $defaultabstract = "USERCARD:VIEWABSTRACTCARD";
    var $cviews = array(
        "USERCARD:VIEWABSTRACTCARD"
    );
    /**
     * @templateController
     * @param string $target
     * @param bool $ulink
     * @param string $abstract
     */
    function viewabstractcard($target = "finfo", $ulink = true, $abstract = "Y")
    {
        // -----------------------------------
        //     doc::viewabstractcard($target,$ulink,$abstract);
        $this->viewprop($target, $ulink, $abstract);
        $this->viewattr($target, $ulink, $abstract);
    }
    
    function postStore()
    {
        $err = $this->RefreshLdapCard();
        
        $this->SetPrivacity(); // set doc properties in concordance with its privacity
        $err.= ($err ? '\n' : '') . $this->updateUsableMail();
        return ($err);
    }
    
    function preRefresh()
    {
        // " gettitle(D,US_IDSOCIETY):US_SOCIETY,US_IDSOCIETY";
        $this->AddParamRefresh("US_IDSOCIETY,US_SOCADDR", "US_WORKADDR,US_WORKTOWN,US_WORKPOSTALCODE,US_WORKWEB,US_WORKCEDEX,US_COUNTRY,US_SPHONE,US_SFAX");
        $this->AddParamRefresh("US_IDSOCIETY", "US_SCATG,US_JOB");
        
        $doc = new_Doc($this->dbaccess, $this->getRawValue("US_IDSOCIETY"));
        if ($doc->isAlive()) {
            if ($this->getRawValue("US_SOCADDR") != "") {
                $this->setValue("US_WORKADDR", $doc->getRawValue("SI_ADDR", " "));
                $this->setValue("US_WORKTOWN", $doc->getRawValue("SI_TOWN", " "));
                $this->setValue("US_WORKPOSTALCODE", $doc->getRawValue("SI_POSTCODE", " "));
                $this->setValue("US_WORKWEB", $doc->getRawValue("SI_WEB", " "));
                $this->setValue("US_WORKCEDEX", $doc->getRawValue("SI_CEDEX", " "));
                $this->setValue("US_COUNTRY", $doc->getRawValue("SI_COUNTRY", " "));
            }
            $this->setValue("US_SCATG", $doc->getRawValue("SI_CATG"));
            $this->setValue("US_JOB", $doc->getRawValue("SI_JOB"));
            
            if ($this->getRawValue("US_PPHONE") != "") $this->setValue("US_PHONE", $this->getRawValue("US_PPHONE") . " (" . _("direct") . ")");
            else $this->setValue("US_PHONE", $doc->getRawValue("SI_PHONE", " "));
            if ($this->getRawValue("US_PFAX") != "") $this->setValue("US_FAX", $this->getRawValue("US_PFAX") . " (" . _("direct") . ")");
            else $this->setValue("US_FAX", $doc->getRawValue("SI_FAX", " "));
        } else {
            $this->setValue("US_PHONE", $this->getRawValue("US_PPHONE", " "));
            $this->setValue("US_FAX", $this->getRawValue("US_PFAX", " "));
        }
    }
    /**
     * refresh LDAP
     */
    function PostDelete()
    {
        $this->SetLdapParam();
        $this->DeleteLdapCard();
    }
    /**
     * test if the document can be set in LDAP
     */
    function canUpdateLdapCard()
    {
        // $priv=$this->getRawValue("US_PRIVCARD");
        $priv = '';
        if ($priv == "S") return false;
        return true;
    }
    /**
     * Set usable mail attribute
     * @return string
     */
    function updateUsableMail()
    {
        $mail = $this->getRawValue("us_mail", $this->getRawValue("us_homemail"));
        return $this->SetValue("us_usablemail", $mail);
    }
    /**
     * return different DN if is a private or not private card
     * @param $rdn
     * @param string $path
     * @return string
     */
    function getUserLDAPDN($rdn, $path = "")
    {
        $priv = $this->getRawValue("US_PRIVCARD");
        if ($priv == "P") {
            $u = new Account("", $this->owner);
            if ($u->isAffected()) {
                $this->infoldap[$this->cindex]["ou"] = $u->login;
                return sprintf("%s=%s,ou=%s,%s,%s", $rdn, $this->infoldap[$this->cindex][$rdn], $u->login, $path, $this->racine);
            }
        } elseif ($priv == "G") {
            $tidg = $this->getMultipleRawValues("us_idprivgroup");
            
            $tdn = array(); // array od DN
            foreach ($tidg as $k => $idg) {
                $t = getTDoc($this->dbaccess, $idg);
                $login = getv($t, "us_login");
                $this->infoldap[$this->cindex]["ou"] = $login;
                $tdn[] = sprintf("%s=%s,ou=%s,%s,%s", $rdn, $this->infoldap[$this->cindex][$rdn], $login, $path, $this->racine);
            }
            if (count($tdn) == 0) return "";
            elseif (count($tdn) == 1) return $tdn[0];
            return $tdn;
        } else {
            return $this->getLDAPDN($rdn, $path);
        }
        return '';
    }
    /**
     * recompute profil with privacy attribute value
     * 5 possibilities :
     *  W : public in read/write
     *  R : public in read mode
     *  P : private
     *  G : group restriction
     *  S : specific profil : do nothing
     */
    function SetPrivacity()
    {
        $priv = $this->getRawValue("US_PRIVCARD");
        $err = "";
        
        switch ($priv) {
            case "P":
                if ($this->profid == "0") {
                    $err = $this->setControl();
                } else {
                    $this->RemoveControl();
                    $err = $this->setControl();
                }
                $err = $this->lock();
                
                break;

            case "R":
                if ($this->profid != "0") {
                    $this->unsetControl();
                }
                $this->lock();
                break;

            case "W":
                if ($this->profid != "0") {
                    $this->unsetControl();
                }
                $this->unlock();
                break;

            case "G":
                if ($this->profid == "0") {
                    $err = $this->setControl();
                } elseif ($this->profid == $this->id) {
                    //already profil :reset
                    $this->RemoveControl();
                    $err = $this->setControl();
                }
                if ($this->profid == $this->id) {
                    
                    $tidg = $this->getMultipleRawValues("us_idprivgroup");
                    foreach ($tidg as $k => $idg) {
                        $t = getTDoc($this->dbaccess, $idg);
                        $gid = getv($t, "us_whatid");
                        
                        $this->AddControl($gid, "view");
                    }
                }
                
                $err = $this->lock();
                
                break;
        }
        if ($err != "") AddLogMsg($this->title . ":" . $err);
    }
    /**
     * return mail address like "john" <john@example.net>
     * @return string
     */
    function getMail()
    {
        $mail = $this->getRawValue("us_usablemail");
        if ($mail) return sprintf('"%s" <%s>', str_replace('"', '-', $this->getTitle()) , $mail);
        return '';
    }
    /**
     * return attribute used to filter from keyword
     * @return string
     */
    static function getMailAttribute()
    {
        return "us_usablemail";
    }
    /**
     * return a mail address in a user-friendly representation, which
     * might not be RFC822-compliant.
     * (e.g. "John Doe (john.doe (at) EXAMPLE.NET)")
     * @return string
     */
    public function getMailTitle()
    {
        return $this->getMail();
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