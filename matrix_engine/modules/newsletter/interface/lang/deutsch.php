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
define("LANG_MODULE_Newsletter_Title","Mailing");
define("LANG_MODULE_Newsletter_Desc","Verwenden Sie die Mailing Verwaltung zum erstellen <br>und versenden von Newslettern. <br><br> Eine Liste der bereits erstellten Newsletter<br> finden Sie im linken Bereich.");

## tabs
define("LANG_MODULE_Newsletter_TabOverview","Zusammenfassung");
define("LANG_MODULE_Newsletter_TabDelivery","Versenden");

## info
define("LANG_MODULE_Newsletter_Created","Erstellt");

## newsletter creation
define("LANG_MODULE_Newsletter_Name","Mailing");
define("LANG_MODULE_Newsletter_NameDesc","Bitte geben Sie den Namen des Mailings ein. Der Name wird auch als Titel in der E-Mail verwendet");

## creation
define("LANG_MODULE_Newsletter_SendTitle","Versenden");
define("LANG_MODULE_Newsletter_SendDesc","Wollen Sie dieses Mailing verschicken?");


## sending mails
define("LANG_MODULE_Newsletter_ProcessingTitle","Bitte warten");
define("LANG_MODULE_Newsletter_ProcessingDesc","Bitte warten Sie w&auml;hrend das Mailing versendet wird. Bitte brechen Sie diesen Vorgang nicht ab.");
define("LANG_MODULE_Newsletter_ProcessingStatus","Bearbeite");


define("LANG_MODULE_Newsletter_DoneSendingTitle","Mailing verschickt");
define("LANG_MODULE_Newsletter_DoneSendingDesc","Das Mailing wurde erfolgreich verschickt");

# select recipeints

define("LANG_MODULE_Newsletter_Recipient","Empf&auml;nger festlegen");
define("LANG_MODULE_Newsletter_RecipientDesc","Bitte w&auml;hlen Sie einen der Filter aus dem Kunden-Modul.");
define("LANG_MODULE_Newsletter_NrRecipients","Empf&auml;nger");

define("LANG_MODULE_Newsletter_DeleteTitle","Mailing l&ouml;schen");
define("LANG_MODULE_Newsletter_DeleteTitleDesc","Wollen Sie das ausgew&auml;hlte Mailing wirklich l&ouml;schen?");


## newsletter status
define("LANG_MODULE_Newsletter_Status","Status");
define("LANG_MODULE_Newsletter_StatusNotSend","Das Mailing wurde noch nicht verschickt");
define("LANG_MODULE_Newsletter_StatusSend","Das Mailing wurde verschickt:");

## mailengine
define("LANG_MODULE_Newsletter_ErrorRecipients","Empf&auml;nger nicht gefunden");
define("LANG_MODULE_Newsletter_ErrorRecipientsDesc","Die Empf&auml;ngerliste konnte nicht gefunden werden. M&ouml;glicherweise wurde der verwendete Filter gel&ouml;scht.");

define("LANG_MODULE_Newsletter_TestMail","Kampagne testen");
define("LANG_MODULE_Newsletter_TestMailDesc","Wir empfehlen Ihnen die erstellte Kampagne zu testen um sicher zu gehen das alle Inhalte so dargestellt werden wie gew&uuml;nscht. Sie k&ouml;nnen soviele Testmails verschicken wie Sie w&uuml;nschen.");

define("LANG_MODULE_Newsletter_ContentTitle","Inhalte definieren");
define("LANG_MODULE_Newsletter_ContentTitleDesc","Bitte erstellen Sie die Inhalte f&uuml;r dieses Mailing.");
?>