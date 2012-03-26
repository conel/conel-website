<?php
## =======================================================================        
##  german.php        
## =======================================================================        
##  holds all strings for a certain languag module
##
##  TODO:   
##     - fill it with the apropritate strings, step by step    
## =======================================================================

## globals
define("LANG_Copyright","&copy; 2001-2006 WORKMATRIX. Alle Rechte vorbehalten.");
define("LANG_NoName","Ohne Titel");

define("LANG_WelcomeMsg","<b>Willkommen bei webmatrix</b><br><br>Das webmatrix Content Management System unterst&uuml;tzt Sie bei der effiziente Organisation von Webseiten und den darin enthaltenen Objekte. Dabei k&ouml;nnen Inhalte schneller und effektiver editiert, gestaltet und bereitgestellt werden. <br><br>Nachfolgend erl&auml;utern wir Ihnen die Funktionsweise von webmatrix.");
define("LANG_TemplateEditor","Vorlagen Editor");
define("LANG_NoAccessRights","<b>Sie haben nicht die erforderlichen Zugriffsrechte.</b><br> Bitte wenden Sie sich an Ihren webmatrix Administrator");

## for selecting laayouts....
define("LANG_SelectLayoutTitle","Bitte w&auml;hlen Sie ein Layout");
define("LANG_SelectLayout","Bitte w&auml;hlen Sie ein Layout f&uuml;r die neue Seite.");
define("LANG_SelectLayoutDescription","Nachdem Sie sich f&uuml;r ein Layout entschieden haben, k&ouml;nnen Sie &uuml;ber eine Eingabemaske alle Daten eingeben.");

## data entry...
define("LANG_EnterData","Bitte geben Sie die Daten f&uuml;r diese Seite ein.");
define("LANG_EnterDataDescription","Dieses Eingabeformular wurde automatisch generiert. Sie haben die M&ouml;glichkeit das Layout der Eingabeformulare &uuml;ber eigene Vorlagen zu steuern.");

## element Tags
define("LANG_ElementText","Text");
define("LANG_ElementLink","Link");
define("LANG_ElementImage","Bild");
define("LANG_ElementList","Liste");

## image language settings
define("LANG_DIFFERENTFILE","Im Internet kann die von Ihnen gewaehlte Datei leider nicht angezeigt werden. Bitte waehlen Sie statt dessen eine Datei im gif oder jpg Format aus.");

## naming the page
define("LANG_PageName","Seitenname");
define("LANG_PageNameDescription","Bitte geben Sie die Namen der Seite ein. Dieser wird in der Navigation sichtbar sein.");
define("LANG_PageReName","Seite umbenennen");
define("LANG_PageReNameSuccess","Der Name der Seite wurde erfolgreich ge&auml;ndert.");

## save page
define("LANG_PageSaved","Seite erfolgreich gespeichert");
define("LANG_PageSavedDescription","Die Seite wurde erfolgreich gespeichert");

## homepage page
define("LANG_Homepage","Homepage");
define("LANG_HomepageDescription","Homepage erfolgreich gesetzt");

## delete page
define("LANG_DeletePage","Seite l&ouml;schen");
define("LANG_DeletePageSuccess","Die Seite wurde erfolgreich gel&ouml;scht");
define("LANG_DeletePageSubpages","Diese Seite enth&auml;lt Unterseiten. Bitte L&ouml;schen Sie diese bevor Sie die Hauptseite l&ouml;schen");
define("LANG_DeletePageWant", "Wollen Sie die Seite");
define("LANG_DeletePageReallyDelete", "wirklich l&ouml;schen?");

## visibilty
define("LANG_Visibility","Sichtbarkeit");
define("LANG_VisibilitySuccess","Die Sichtbarkeit der Seite wurde erfolgreich ge&auml;ndert");

## visibilty
define("LANG_SortPages","Sortieren");
define("LANG_SortPagesDescription","Bitte w&auml;hlen Sie eine Sortiermethode und klicken Sie anschlie&szlig;end auf <i>sortieren</i>.");

## user management
define("LANG_UserEnterData","<b>Bitte geben Sie die Daten f&uuml;r diesen Nutzer ein.</b>");
define("LANG_UserEnterDataDescription","<p>Hier k&ouml;nnen Sie die Daten und Zugriffsrechte f&uuml;r diesen Nutzer einstellen.</p>");
define("LANG_UserName","<b>Benutzername</b>");
define("LANG_UserNameDescription","<p><i>Dies ist der Login-Name des Nutzer</i></p>");
define("LANG_UserMail","<b>E-Mail</b>");
define("LANG_UserMailDescription","<p><i>Die E-Mail Adresse ist wichtig, um dem Nutzer Neuerung oder &Auml;nderungen automatisch zu schicken.</i></p>");
define("LANG_UserPassword","Kennwort");
define("LANG_UserPasswordDescription","Lassen Sie das Passwort-Feld leer, wenn Sie das aktuelle Passwort beibehalten wollen");

define("LANG_UserAdministration","Benutzer Verwaltung");
define("LANG_UserSaved","Benutzer gespeichert");
define("LANG_UserSavedSuccess","Der Benutzer wurde erfolgreich gespeichert");
define("LANG_UserChanged","Benutzer aktualisiert");
define("LANG_UserChangedSuccess","Der Benutzer wurde erfolgreich aktualisiert");
define("LANG_UserDeleted","Benutzer gel&ouml;scht");
define("LANG_UserDeletedSuccess","Der Benutzer wurde erfolgreich aktualisiert");
define("LANG_UserDescription","Verwalten Sie auf einfache Art und Weise die Nutzer Ihrer webmatrix Installation. <br><br> Eine Liste der Nutzer finden Sie im linken Bereich");

## access rights
define("LANG_UserAcessRights","Zugriffsrechte");
define("LANG_UserAcessEditor","Seiten erstellen, &auml;ndern und l&ouml;schen");
define("LANG_UserAcessTemplate","Vorlagen verwalten");
define("LANG_UserAcessUsers","Nutzer verwalten");


## template editor
define("LANG_TemplateDescription","Ben&uuml;tzen Sie den Vorlagen Editor, um Vorlagen zu erstellen,<br> zu &auml;ndern oder zu l&ouml;schen.<br><br> Sie finden eine Liste der Vorlagen auf der linken Seite.");
define("LANG_TemplateDeleted","Vorlage gel&ouml;scht");
define("LANG_TemplateDeletedSuccessfull","Die Vorlage wurde erfolgreich gel&ouml;scht");
define("LANG_TemplateChanged","Vorlage ge&auml;ndert");
define("LANG_TemplateChangedSuccessfull","Die Vorlage wurde erfolgreich ge&auml;ndert");
define("LANG_TemplateSaved","Vorlage gespeichert");
define("LANG_TemplateSavedSuccessfull","Die Vorlage wurde erfolgreich gespeichert");

define("LANG_TemplateEnterData","Bitte geben Sie die Daten f&uuml;r diese Vorlage ein");
define("LANG_TemplateEnterDataDescription","Bitte geben Sie einen Namen und die Beschreibung f&uuml;r diese Vorlage ein.");
define("LANG_TemplateName","Name");
define("LANG_TemplateNameDescription","Der Name hilft dem Redakteur bei der Auswahl der richtigen Vorlage.");
define("LANG_TemplateDesc","Beschreibung");
define("LANG_TemplateDescDescription","Eine n&auml;here Beschreibung der Vorlage");
define("LANG_TemplateFile","Dateiname");
define("LANG_TemplateFileDescription","Geben Sie hier den Dateinamen der Vorlage ein.");
define("LANG_TemplateThumb","Thumbnail");
define("LANG_TemplateThumbDescription","W&auml;hlen Sie ein Bild das dieses Layout repr&auml;sentiert");


## context menues
define("LANG_MenuNewSubPage","Neue Seite...");
define("LANG_MenuEdit","Bearbeiten...");
define("LANG_MenuDelete","L&ouml;schen");
define("LANG_MenuPreview","Vorschau...");
define("LANG_MenuRename","Umbenennen...");
define("LANG_MenuChangeVisibility","Aktiv/Inaktiv");
define("LANG_MenuChangeMenuState","Im Men&uuml; ein/aus");
define("LANG_MenuSort","Sortieren");
define("LANG_MenuInfo","Informationen");
define("LANG_SetHomepage","Homepage");

## user management
define("LANG_MenuInfoNewUser","Neuer Nutzer...");


## added in version 1.4.1
define("LANG_Language","Sprachauswahl");
define("LANG_LoginError","Log-In Fehler");
define("LANG_NamePassUnknown","Name und Passwort unbekannt.");


## added in version 1.4.2
define("LANG_DeleteElementDescription", "Wollen Sie dieses Element wirklich loeschen");

## added in version 1.4.8
define("LANG_MONTH", "Monat");
define("LANG_DAY", "Tag");
define("LANG_YEAR", "Jahr");

## added in version 1.6.7
define("LANG_HOURS", "Stunden");
define("LANG_MINUTES", "Minuten");
define("LANG_SECONDS", "Sekunden");


define("LANG_TemplateParent","Hauptvorlage");
define("LANG_TemplateParentDescription","Geben Sie hier den Namen der Vorlage an, die vor dieser Vorlage kommen muss");

## added in version 1.6.7
define("LANG_Template", "Template/Vorlage");
define("LANG_TemplateUsage", "Die von Ihnen gew&auml;hlte Seite verwendet das folgende Template<br>");


## added in version 1.8
## the tabs
define("LANG_TAB_PAGE", "ページ");
define("LANG_TAB_TEMPLATES", "テンプレート");
define("LANG_TAB_USER", "ユーザー");

## menu
define("LANG_MENU_NEWFOLDER", "Neuer Ordner");
define("LANG_MENU_NEWPAGE", "Neue Seite");

## added in version 1.9
## version control
define("LANG_VERSION_Description", "Folgende Versionen sind f&uuml;r dieses Dokument vorhanden.");

## added in version 1.9.1
## version control
define("LANG_VERSION_Approved", "Seite freigeben");
define("LANG_VERSION_Approved_Desc", "Die Seite wurde freigegeben.");

define("LANG_VERSION_Activate", "Seite wiederhergestellt.");
define("LANG_VERSION_Activate_Desc", "Die von Ihnen gew&auml;hlte Version wurde erfolgreich wiederhergestellt.");

## added in order to support levels in templates
define("LANG_TemplateLevels","Template auf Ebenen beschr&auml;nken");
define("LANG_TemplateLevelsDesc","Wenn Sie die verwendung des Templates auf bestimmte hierachie Ebenen einschr&auml;nken wollen, k&ouml;nnen Sie die Ebene hier festlegen.");

define("LANG_TemplateLevelsNoneSelected","Kein Level ausgew&auml;hlt");
?>