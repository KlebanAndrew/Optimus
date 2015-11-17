<?php
$ldaprdn  = $_POST["login"]."@ifobl.if.energy.gov.ua";
if(! $_POST["pass"]) { 
	$ldappass = 'dfghdfghdfhdfghdf';
} else {
	$ldappass = $_POST["pass"];
}
$ldapconn = ldap_connect("ldap://10.93.1.59/") or die("Неможливо з`єднатися з сервером AD !");
if ($ldapconn) {
    ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
    $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    if ($ldapbind) {
        echo "TRUE";
	} else {
        echo "FALSE";
    }
}
?>