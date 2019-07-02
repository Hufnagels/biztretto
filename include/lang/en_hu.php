<?php
header('Content-type: text/html; charset=utf-8');

unset( $lang);
/**********************************************
table names and rows 
**********************************************/
/* 

CREATE TABLE `irodak` (
  `office_id` int(11) NOT NULL default '0',
  `office_nametag` text collate utf8_unicode_ci NOT NULL,
  `office_name` text collate utf8_unicode_ci NOT NULL,
  `credit` int(11) NOT NULL default '0',
  `credit_usable` int(11) NOT NULL default '0',
  `credit_safe` int(11) NOT NULL default '0',
  `office_ident_number` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `office_tax_number` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `office_email` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `office_tel` text collate utf8_unicode_ci NOT NULL,
  `office_postcode` int(11) NOT NULL default '0',
  `office_city` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `office_street` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `letter_is` int(2) NOT NULL default '0',
  `letter_postcode` int(11) NOT NULL default '0',
  `letter_city` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `letter_street` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `contact_name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `contact_email` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `contact_title` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `contact_tel` text collate utf8_unicode_ci NOT NULL,
  `comment` text collate utf8_unicode_ci NOT NULL,

) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
*/

$lang['office_id'] = 'azonosító';
$lang['office_nametag'] = 'Irodai azonosító';
$lang['office_name'] = 'Cégnév';
$lang['credit'] = 'Egyenleg';
$lang['credit_usable'] = 'Felhasználható egyenleg';
$lang['credit_safe'] = 'Tartalék';
$lang['office_ident_number'] = 'Cégjegyzékszám';
$lang['office_tax_number'] = 'Adószám';
$lang['office_email'] = 'Központi E-mail';
$lang['office_tel'] = 'Központi Telefon/Fax';
$lang['office_postcode'] = 'Irányítószám';
$lang['office_city'] = 'Város';
$lang['office_street'] = 'Utca';
$lang['letter_is'] = 'Számlázási/Levelezési cím';
$lang['letter_postcode'] = 'Irányítószám';
$lang['letter_city'] = 'Város';
$lang['letter_street'] = 'Utca';
$lang['contact_name'] = 'Cégvezető neve';
$lang['contact_email'] = 'Cégvezető email';
$lang['contact_title'] = 'Cégvezető beosztása';
$lang['contact_tel'] = 'Cégvezető telefon';
$lang['comment'] = 'Megjegyzés';


$lang['registered'] = 'Regisztrálva';
$lang['update_time'] = 'Frissítve';
$lang['banned'] = 'Tiltott';
$lang['office_data'] = 'Cégadatok';

/* 
CREATE TABLE `u` (
  `u_id` bigint(20) NOT NULL auto_increment,
  `md5_id` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `office_nametag` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `full_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `user_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `user_email` varchar(220) collate utf8_unicode_ci NOT NULL default '',
  `userlevel` tinyint(4) NOT NULL default '1',
  `parent_id` bigint(20) NOT NULL default '0',
  `user_credit` int(11) NOT NULL default '0',
  `group` int(11) NOT NULL default '0',
  `group_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `pwd` varchar(220) collate utf8_unicode_ci NOT NULL default '',
  `address` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `office_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `user_tel` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `user_fax` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `registered` datetime NOT NULL default '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `users_ip` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `approved` int(1) NOT NULL default '0',
  `activation_code` int(10) NOT NULL default '0',
  `activation_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `banned` int(1) NOT NULL default '0',
  `ckey` varchar(220) collate utf8_unicode_ci NOT NULL default '',
  `ctime` varchar(220) collate utf8_unicode_ci NOT NULL default '',
  `profilePicture` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `depth` bigint(20) NOT NULL default '0',
  `lft` bigint(20) NOT NULL default '0',
  `rgt` bigint(20) NOT NULL default '0',
  `design_id` bigint(20) NOT NULL default '0',
  `background_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `header_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `content_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `sidebar_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `text_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `link_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `footer_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`u_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=152 ;
*/


$lang['u_id'] = 'azonosító';
$lang['md5_id'] = 'md5 azonosító';
$lang['office_nametag'] = 'Irodai azonosító';
$lang['full_name'] = 'Teljes név';
$lang['user_name'] = 'Felhasználónév';
$lang['user_email'] = 'Email cím';
$lang['userlevel'] = 'Felhasználói szint';
$lang['parent_id'] = 'parent azonosító';
$lang['user_credit'] = 'Felhasználó egyenlege';
$lang['group'] = 'Csoport';
$lang['group_name'] = 'Csoport azonosító';
$lang['pwd'] = 'Jelszó';
$lang['address'] = 'Cím';
$lang['office_name'] = 'Cégnév';
$lang['user_tel'] = 'Telefon';
$lang['user_fax'] = 'Fax';
$lang['registered'] = 'Regisztrálva';
$lang['update_time'] = 'Frissítve';
$lang['users_ip'] = 'IP';
$lang['approved'] = '';
$lang['activation_code'] = 'Aktiváló kód';
$lang['activation_time'] = 'Áktiválás ideje';
$lang['banned'] = 'Kitiltva';
$lang['ckey'] = '';
$lang['ctime'] = '';
$lang['profilePicture'] = 'Saját fotó';
$lang['depth'] = 'szint';
$lang['lft'] = 'bal.pont';
$lang['rgt'] = 'jobb.pont';
$lang['design_id'] = '';
$lang['background_color'] = 'Háttér';
$lang['text_color'] = 'Szöveg';
$lang['link_color'] = 'Link';
$lang['content_color'] = 'Tartalom';
$lang['sidebar_color'] = 'Oldalsáv';
$lang['header_color'] = 'Fejléc';
$lang['footer_color'] = 'Lábléc';

$lang['user_data'] = 'Profil adatok';
/* 
CREATE TABLE `private` (
  `u_id` bigint(20) NOT NULL auto_increment,
  `md5_id` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `office_nametag` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `full_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `user_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `user_email` varchar(220) collate utf8_unicode_ci NOT NULL default '',
  `userlevel` tinyint(4) NOT NULL default '1',
  `parent_id` bigint(20) NOT NULL default '0',
  `user_credit` int(11) NOT NULL default '0',
  `group` int(11) NOT NULL default '0',
  `group_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `pwd` varchar(220) collate utf8_unicode_ci NOT NULL default '',
  `address` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `office_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `user_tel` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `user_fax` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `registered` datetime NOT NULL default '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `users_ip` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `approved` int(1) NOT NULL default '0',
  `activation_code` int(10) NOT NULL default '0',
  `activation_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `banned` int(1) NOT NULL default '0',
  `ckey` varchar(220) collate utf8_unicode_ci NOT NULL default '',
  `ctime` varchar(220) collate utf8_unicode_ci NOT NULL default '',
  `profilePicture` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `depth` bigint(20) NOT NULL default '0',
  `lft` bigint(20) NOT NULL default '0',
  `rgt` bigint(20) NOT NULL default '0',
  `design_id` bigint(20) NOT NULL default '0',
  `background_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `header_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `content_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `sidebar_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `text_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `link_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  `footer_color` varchar(6) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`u_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=152 ;
*/
$lang['person_email'] = 'Email cím';
$lang['person_tel'] = 'Telefon';
$lang['user_credit'] = 'Felhasználó egyenlege';
$lang['person_pwd'] = 'Jelszó';
$lang['person_pwd2'] = 'Jelszó újra';


$lang['registered'] = 'Regisztrálva';
$lang['update_time'] = 'Frissítve';
$lang['banned'] = 'Tiltott';



/* registration form, cegadatok form */
$lang['office_base_data'] = 'Cégadatok';
$lang['office_seat_data'] = 'Székhely címe';
$lang['different_address'] = 'Eltérő számlázási/levelezési cím';
$lang['contact'] = 'Kapcsolattartó';
$lang['office'] = 'Céges';
$lang['personel'] = 'Személyes';

/* timestamp */
$lang['seconds ago'] = ' másodperce';
$lang['one minute ago'] = 'egy perce';
$lang['minutes ago'] = ' perce';
$lang['one hour ago'] = 'egy órája';
$lang['hours ago'] = ' órája';
$lang['one day ago'] = 'egy napja';
$lang['days ago'] = ' napja';
$lang['one week ago'] = 'egy hete';
$lang['weeks ago'] = ' hete';
$lang['one month ago'] = 'egy hónapja';
$lang['months ago'] = ' hónapja';
$lang['one year ago'] = 'egy éve';
$lang['years ago'] = ' éve';

#A
$lang['accentuate'] = 'Kiemelés';
$lang['accentuated'] = 'Kiemelt';

$lang['active'] = 'Aktív';
$lang['admin_page'] = 'Adminisztrációs felület';

$lang['addressant'] = 'Címzett';
$lang['adjust'] = 'Beállítások';

$lang['advert'] = 'Hirdetés';
$lang['advert_new'] = 'Új hirdetés';
$lang['advert_id'] = 'Hirdetés azonosító';
$lang['advert_details'] = 'Részletek';
$lang['adverts'] = 'Hirdetések';
$lang['advert_pack'] = 'Hirdetési csomag';
$lang['advert_search'] = 'Hirdetés keresés';
$lang['advert_office'] = 'Iroda hirdetései';

$lang['advise'] = 'Kiajánl';
$lang['agree_contract'] = 'Fogadja el az általános szerződési feltételeket';

#B
$lang['base'] = 'Alapadatok';
$lang['base_size'] = 'Alapterület';
$lang['ballance_updated'] = 'Egyenleg sikeresen módosítva!';

#C
$lang['choose'] = 'Válasszon...';

$lang['cold_call'] = 'Hideghívás';

$lang['confirm'] = 'Jóváhagyás';


$lang['cookies_enabled'] = 'Sütik használata engedélyezve';
$lang['cookies_disabled'] = 'Az oldal működéséhez engedélyeznie kell a sütik használatát!';

$lang['correct_data'] = 'Adjon meg érvényes adatot';

$lang['credit_plus'] = 'Jóváírás';
$lang['credit_new'] = 'Új egyenleg';

$lang['credit_updated'] = 'Sikeres egyenleg feltöltés!';
$lang['credit_ballance_updated'] = 'Sikeresen beállította a felhasználható egyenleget!';

#D
$lang['delete'] = 'Törlés';
$lang['deleted'] = 'Törölt';
$lang['is_deleted'] = 'Törölve';
$lang['is_deletable'] = 'törölhető';
$lang['not_deletable'] = 'nem törölhető';
$lang['description'] = 'Leírás';
$lang['details'] = 'Részletek';


#E
$lang['edit'] = 'Szerkesztés';
$lang['error_error'] = 'Hiba';
$lang['error'] = 'Hiba';
$lang['error_deleting_item'] = 'Nem sikerült a törlés!';
$lang['error_insert'] = 'Hiba a beszúrásnál';
$lang['error_inserting_item'] = 'Nem sikerült a beszúrás!';
$lang['error_update'] = 'Hiba a frissítésnél';
$lang['error_updating_item'] = 'Sikertelen a frissítés!';

#F



#H
$lang['highlite'] = 'Kiemelés';

#I
$lang['id'] = 'Azonosító';
$lang['image_sort'] = 'Képek rendezése';
$lang['image_upload'] = 'Kép feltöltése';
$lang['inactive'] = 'Inaktív';
$lang['info'] = 'Információ';
$lang['in_progress'] = 'Feldolgozás folyamatban...';


#L
$lang['list'] = 'Lista';
$lang['login'] = 'Belépés';
$lang['logout'] = 'Kilépés';
$lang['logout_time_restrict'] = 'Időtúllépés...';

#M
$lang['message'] = 'Üzenet';
$lang['messages'] = 'Üzenetek';
$lang['mobile'] = 'Mobil';
$lang['mod'] = 'Módosítás';
$lang['mod_pwd'] = 'Jelszó módosítása';
$lang['my_data'] = 'Adataim';

#N
$lang['name'] = 'Név';

$lang['name_login'] = 'Felhasználói név';
$lang['need_js'] = 'Engedélyezni kell a javascript-et az űrlap használatához!';
$lang['new'] = 'Új létrehozása';
//$lang['new'] = 'Új';

#O

$lang['office'] = 'Iroda';
$lang['owner'] = 'Tulajdonos';
$lang['offers'] = 'Kiajánlás';
$lang['operation'] = 'Műveletek';

#P
$lang['person'] = 'Magánszemély';
$lang['persons'] = 'Magánszemélyek';

$lang['price'] = 'Ár';
$lang['pwd'] = 'Jelszó';
$lang['pwd_old'] = 'Régi jelszó';
$lang['pwd_new'] = 'Új jelszó';
$lang['pwd_new_again'] = 'Jelszó újra';

/* PASSWORD STRENGHT */
$lang['pw_short'] = 'Túl rövid';
$lang['pw_unsafe'] = ' Nem biztonságos jelszó!';
$lang['pw_min'] = ' Minimum hossz: ';
$lang['pw_1'] = 'Nagyon gyenge';
$lang['pw_2'] = 'Gyenge';
$lang['pw_3'] = 'Közepes';
$lang['pw_4'] = 'Jó';
$lang['pw_5'] = 'Kiváló';

#Q
$lang['q_logout'] = ' Biztosan ki akar lépni?';

#R
$lang['referer'] = 'Referens';
$lang['referer_created'] =' Referens létrehozva ';
$lang['referer_create_new'] =' Új referens létrehozása ';
$lang['referer_deleted'] =' Referens törölve ';
$lang['referer_modify_nested'] = ' Referens struktúra módosítása ';
$lang['referer_not_marked'] = 'Nincs referens megjelölve';
$lang['referer_updated'] =' Referens adatai frissítve ';
$lang['referer_save_nested'] = ' Referens struktúra mentése ';
$lang['referer_str_saved_succ'] = ' Referens struktúra sikeresen rögzítve! ';
$lang['referer_str_saved_unsucc'] = ' Referens struktúra rögzítése sikertelen! ';

$lang['referents'] = 'Referensek';
$lang['referents_manage'] = 'Referensek kezelése';

$lang['registration'] = 'Feladás';

#S
$lang['save'] = 'Mentés';
$lang['saved_last'] = 'Utolsó mentés';
$lang['saved_succ'] = 'Sikeresen rögzítve!';

$lang['search'] = 'Keresés';
$lang['spec_search'] = 'Részletes keresés';
$lang['stat'] = 'Statisztika';
$lang['status'] = 'Státusz';
$lang['stay'] = 'Maradok';


#T
$lang['title'] = 'Titulus';

#U

$lang['username_exsist'] = ' A felhasználói név foglalt! ';
$lang['username_not_changed'] = ' Nem módosította a felhasználói nevet! ';

#W
$lang['warning'] = ' Figyelmeztetés...';

#long_messages

/*  */
$lang['message1'] = 'Túllépte az űrlap kitöltésére rendelkezésre álló időt (10 perc)!';
$lang['message2'] = 'Azonosítás sikeres volt!';
$lang['message3'] = 'Jogosulatlan kérés!';

/* save_pass.php */
$lang['message4'] = 'Jelszó módosítva';
$lang['message5'] = 'A megadott két jelszó eltérő';

/* ajax call messages */
$lang['message6'] = 'Nem megfelelő a böngésző, amit használ. Az oldal nem jeleníthető meg!';
$lang['message7'] = 'Hiba, a keresett file nem található. Lépjen kapcsolatba kollégáinkkal!';

/* form_add_referens */
$lang['message8'] = 'Ne felejtse el a struktúrát menteni!!!';

/* form_hirdetes_list_new */
$lang['message9'] = 'Loading data from server';
$lang['mod_advert_status'] = ' Hirdetés státuszának módosítása ';
$lang['set_advert_to_ref'] = ' Hirdetés áthelyezése másik referenshez ';
$lang['drag_here_adverts'] = ' Húzza ide a kiajánlani kívánt hirdetést! ';

$lang['new_owner'] = ' A hirdetés új hirdetője: ';
$lang['new_owners'] = ' A hirdetések új hirdetője: ';

$lang['owner_successfully_updated'] = ' A hirdető sikeresen módosítva. ';

$lang['new_status_advert'] = ' Hirdetés státusza: ';
$lang['new_status_adverts'] = ' Hirdetések státusza: ';

$lang['advise_advert_to'] = ' Hirdetés kiajánlása ';
$lang['add_selected_data'] = ' Hirdetés listába ';
$lang['auto_address'] = 'A rendszer automatikusan generálja a formalevelet az alábiak szerint: <br>levél tárgya (pl.: kiadó ház , <br>a címzett megszólitását (Tisztelt XY! ),<br> a kiajánlás szövegét (ZZ figyelmébe ajánlja - hirdetésfelsorolás) <br>ide jön az Ön által megírt egyedi üzenet <br> a levél zárása (Üdvözlettel: <br> ZZ <br> ingatlan referens<br> cégnév<br> telefon';
$lang['status_successfully_updated'] = ' Sikeresen módosította a hirdetés státuszát! ';
$lang['none_selected'] = ' Nem választott hirdetést! ';
$lang['subject_1'] = 'A ... Hirdetési portálról  ';
$lang['subject_2'] = ' a következő hirdetéseket ajánlja figyelmébe';

/* save_adatok.php */
$lang['message10'] ='A mentés sikeres volt!';
$lang['message11'] ='A mentés sikertelen volt!';
$lang['message12'] ='A hiba oka';

/* dbc.php auto_logout */
$lang['message13'] ='Az elmúlt 20 percben nem hajtott végre semmilyen műveletet.';
$lang['message14'] ='A rendszer ';
$lang['message15'] =' másodperc múlva automatikusan kilépteti. Amennyiben folytatni kívánja a munkát, úgy kattintson a "Maradok" gombra.';
$lang['message15_1'] =' másodperc múlva automatikusan kilépteti.'; 
$lang['message15_2'] =' Folytatom a munkát.';

/* login.php */
$lang['message16'] ='Inaktivitás miatt a rendszer automatikusan kiléptette.';
$lang['message17'] ='Inaktivitás miatt a rendszer automatikusan kiléptette.';
$lang['message18'] ='Inaktivitás miatt a rendszer automatikusan kiléptette.';

/* form_hirdetes */

?>