<?php
/**
 * User: imyxz
 * Date: 2017/5/24
 * Time: 14:18
 * Github: https://github.com/imyxz/
 */
define('_DS_',DIRECTORY_SEPARATOR);          //Ä¿Â¼·Ö¸ô·û
define('_Root',dirname(__FILE__) . _DS_);
define('_Slimvc',_Root . 'Slimvc' . _DS_);
define('_Controller',_Root . 'Controller' . _DS_);
define('_Model',_Root . 'Model' . _DS_);
define('_View',_Root . 'View' . _DS_);
define('_Class',_Root . 'Class' . _DS_);

include(_Root . 'Config.php');
include(_Root . 'Slimvc.php');
