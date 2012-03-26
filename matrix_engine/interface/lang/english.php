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
define("LANG_Copyright","&copy; 2001-2006 WORKMATRIX. All rights reserved");
define("LANG_NoName","Untitled");

define("LANG_WelcomeMsg","<b>Welcome to webmatrix</b><br> On the left you can see the structure of your website. <br><br> To create a new page press the \"New Page\" button.");
define("LANG_TemplateEditor","Template-Editor");
define("LANG_NoAccessRights","<b>You don't have the required access rights.</b><br> Please contact your webmatrix administrator.");

## for selecting laayouts....
define("LANG_SelectLayoutTitle","Please select a Template");
define("LANG_SelectLayout","Please select a Template for the new page");
define("LANG_SelectLayoutDescription","After selecting the template you will be able to enter content for the new page.");

## data entry...
define("LANG_EnterData","Data entry");
define("LANG_EnterDataDescription","Please enter content for this page.");

## element Tags
define("LANG_ElementText","Text");
define("LANG_ElementLink","Link");
define("LANG_ElementImage","Image");
define("LANG_ElementList","List");

## naming the page
define("LANG_PageName","Pagename");
define("LANG_PageNameDescription","Please enter a name for this page. The Name will be shown in the navigation area of your site.");
define("LANG_PageReName","Rename page");
define("LANG_PageReNameSuccess","The name of the page was changed successfully.");

## save page
define("LANG_PageSaved","Page saved");
define("LANG_PageSavedDescription","The page has been saved successfully");

define("LANG_PageInfo","Page information");
define("LANG_PageInfoDesc","Compact information about the page you selected.");
define("LANG_PageInfo_Created","Created");
define("LANG_PageInfo_Modified","Modified");
define("LANG_PageInfo_PageId","Page id");
define("LANG_PageInfo_URL","Url");
define("LANG_PageInfo_Template", "Template");


## homepage page
define("LANG_Homepage","Homepage");
define("LANG_HomepageDescription","Homepage set successfully");

## delete page
define("LANG_DeletePage","Delete page");
define("LANG_DeletePageSuccess","The Page has been deleted successfully");
define("LANG_DeletePageSubpages","This page has subpages. Please delete the subpages first before deleting this page.");
define("LANG_DeletePageWant", "Do you really want to delete the page");
define("LANG_DeletePageReallyDelete", " ");

## visibilty
define("LANG_Visibility","Visibility");
define("LANG_VisibilitySuccess","The visibility of this page has been changed successfully");

## visibilty
define("LANG_SortPages","Page order");
define("LANG_SortPagesDescription","Please select a order-method from the pulldown menu below.");

## user management
define("LANG_UserEnterData","<b>Please enter the user details.</b>");
define("LANG_UserEnterDataDescription","<p>You can edit the user details and access rights.</p>");
define("LANG_UserName","<b>Username</b>");
define("LANG_UserNameDescription","<p><i>This is the username for logging in.</i></p>");
define("LANG_UserMail","<b>eMail</b>");
define("LANG_UserMailDescription","<p><i> </i></p>");
define("LANG_UserPassword","Password");
define("LANG_UserPasswordDescription","Leave this field empty if you don't wnat to change the password");


define("LANG_UserAdministration","User administration");
define("LANG_UserSaved","User saved");
define("LANG_UserSavedSuccess","The user details were successfully saved");
define("LANG_UserChanged","User changed");
define("LANG_UserChangedSuccess","The user details were successfully changed");
define("LANG_UserDeleted","User deleted");
define("LANG_UserDeletedSuccess","The user was successfully deleted");
define("LANG_UserDescription","");


## access rights
define("LANG_UserAcessRights","Access rights");
define("LANG_UserAcessEditor","create, edit and delete pages");
define("LANG_UserAcessTemplate","edit templates");
define("LANG_UserAcessUsers","create, edit and delete users");


## template editor
define("LANG_TemplateDescription","Use the template editor to create new templates.");
define("LANG_TemplateDeleted","Template deleted");
define("LANG_TemplateDeletedSuccessfull","The template was deleted successfully");
define("LANG_TemplateChanged","Template changed");
define("LANG_TemplateChangedSuccessfull","The template was changed successfully");
define("LANG_TemplateSaved","Template saved");
define("LANG_TemplateSavedSuccessfull","The template was saved successfully saved");

define("LANG_TemplateEnterData","Please enter the data for this template");
define("LANG_TemplateEnterDataDescription","Please enter the name and description of this template");
define("LANG_TemplateName","Name");
define("LANG_TemplateNameDescription","The name helps the editor to choose the right template.");
define("LANG_TemplateDesc","Description");
define("LANG_TemplateDescDescription","The description for this template");
define("LANG_TemplateFile","Filename");
define("LANG_TemplateFileDescription","The filename for this template.");
define("LANG_TemplateThumb","Thumbnail");
define("LANG_TemplateThumbDescription","Please select an image that represents this template.");


## context menues
define("LANG_MenuNewSubPage","New subpage...");
define("LANG_MenuEdit","Edit...");
define("LANG_MenuDelete","Delete");
define("LANG_MenuPreview","Preview...");
define("LANG_MenuRename","Rename...");
define("LANG_MenuChangeVisibility","Deactivate/Activate");
define("LANG_MenuChangeMenuState","Show/Hide in menu");
define("LANG_MenuSort","Sort...");
define("LANG_MenuMove","Move");
define("LANG_MenuInfo","Information");
define("LANG_MenuTimer","Timer");
define("LANG_SetHomepage","Homepage");


## user management
define("LANG_MenuInfoNewUser","New user...");

## added in version 1.4.1
define("LANG_Language","language");
define("LANG_LoginError","Log-In error");
define("LANG_NamePassUnknown","Name and passwort invalid.");

## added in version 1.4.2
define("LANG_DeleteElementDescription", "Do you really want to delete this item?");

## added in version 1.4.8
define("LANG_MONTH", "month");
define("LANG_DAY", "day");
define("LANG_YEAR", "year");

## added in version 1.6.7
define("LANG_HOURS", "hours");
define("LANG_MINUTES", "minutes");
define("LANG_SECONDS", "seconds");


define("LANG_TemplateParent","Parent Template");
define("LANG_TemplateParentDescription","Enter the name of the template this template should be linked to.");

## added in version 1.8
## the tabs
define("LANG_TAB_PAGE", "Pages");
define("LANG_TAB_TEMPLATES", "Templates");
define("LANG_TAB_USER", "User");

## menu
define("LANG_MENU_NEWFOLDER", "New folder");
define("LANG_MENU_NEWPAGE", "New page");

## added in order to support levels in templates
define("LANG_TemplateLevels","Limit template use");
define("LANG_TemplateLevelsDesc","You can restrict the use of a template to a certain hierachy level");

define("LANG_TemplateLevelsNoneSelected","No level selected");

define("LANG_ROOTPAGE", "Root level");
define("LANG_MovePage", "Move page");
define("LANG_MovePageDesc", "Please select the new parent page");

define("LANG_MovePageSuccess", "Page successfully moved");
define("LANG_MovePageSuccessDesc", "The selected page has been moved successfully.");

## version added for 2.0
define("LANG_VERSION_VersionExistsTitle", "Page has beend modified");
define("LANG_VERSION_VersionExistsDesc", "Do you want to sign-off the changes of this page?");

define("LANG_VERSION_PageLocked", "Page locked");
define("LANG_VERSION_PageLocked_Desc", "The page is currently locked for editing.");


## added version 3.0
define("LANG_MENU_COPYPAGE", "Copy Page");
define("LANG_CopyPageDesc", "Do you really want to copy this page?");
define("LANG_CopyPageSuccess", "The page was copied successfully.");

## added version 3.0.1
define("LANG_MENU_COPYSTRUCTURE", "Copy Structure");
define("LANG_CopyStructureDesc", "Do you really want to copy this page and all its subpages?");
define("LANG_CopyStructureSuccess", "The pages were copied successfully.");

// nkowald - 2009-10-06 - Added a new right-click menu item
define("LANG_MENU_REDIRECTPAGE", "Redirect Page");
?>