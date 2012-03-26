<?php
## =======================================================================        
##  english.php        
## =======================================================================        
##  holds all strings for a certain languag module
##
##  TODO:   
##     - fill it with the apropritate strings, step by step    
## =======================================================================

## globals
define("LANG_MODULE_Newsletter_Title","Newsletter");
define("LANG_MODULE_Newsletter_Desc","The Newsletter module can be used to create and send newsletters.<br><br> A list of archvied Newsletters can be found on the left hand side.");

## tabs
define("LANG_MODULE_Newsletter_TabOverview","Overview");
define("LANG_MODULE_Newsletter_TabDelivery","Delivery");

## info
define("LANG_MODULE_Newsletter_Created","Created");


## newsletter creation
define("LANG_MODULE_Newsletter_Name","Newsletter");
define("LANG_MODULE_Newsletter_NameDesc","Please enter a name for the new newsletter.");

## creation
define("LANG_MODULE_Newsletter_SendTitle","Delivery");
define("LANG_MODULE_Newsletter_SendDesc","Do you reall want to send this newsletter");

## sending mails
define("LANG_MODULE_Newsletter_ProcessingTitle","Please wait");
define("LANG_MODULE_Newsletter_ProcessingDesc","Please wait while the newsletter is beeing send. <br>Do not close this window.");
define("LANG_MODULE_Newsletter_ProcessingStatus","Currently processing");

define("LANG_MODULE_Newsletter_DoneSendingTitle","Newsletter send");
define("LANG_MODULE_Newsletter_DoneSendingDesc","The newsletter has been successfully send.");

# select recipeints

define("LANG_MODULE_Newsletter_Recipient","Recipients");
define("LANG_MODULE_Newsletter_RecipientDesc","Please define the recipients for this newsletter.");
define("LANG_MODULE_Newsletter_NrRecipients","recipients");

define("LANG_MODULE_Newsletter_DeleteTitle","Delete newsletter");
define("LANG_MODULE_Newsletter_DeleteTitleDesc","Do you really want to delete the selected newsletter?");


## newsletter status
define("LANG_MODULE_Newsletter_Status","Status");
define("LANG_MODULE_Newsletter_StatusNotSend","The newsletter has not been sent.");
define("LANG_MODULE_Newsletter_StatusSend","The Newsletter was send on: ");

## mailengine
define("LANG_MODULE_Newsletter_ErrorRecipients","Recipients not found");
define("LANG_MODULE_Newsletter_ErrorRecipientsDesc","The recipients could not be found. It's possible that the filter that was used to select the recipients doesn't exist anymore.");


define("LANG_MODULE_Newsletter_TestMail","Test mail");
define("LANG_MODULE_Newsletter_TestMailDesc","Please enter the email address that should recieve the test mail.");

define("LANG_MODULE_Newsletter_ContentTitle","Content");
define("LANG_MODULE_Newsletter_ContentTitleDesc","Please create the content for this newsletter");
?>