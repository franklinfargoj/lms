<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


define('ASSETS', 'assets2/');
define('PLUGINS', 'assets2/plugins/');


/*Tables Name*/
define('Tbl_Category', 'db_master_product_category');
define('Tbl_Products', 'db_master_products');
define('Tbl_ProductDetails', 'db_master_product_details');
define('Tbl_State', 'db_master_state');
define('Tbl_Faq', 'db_faq');
define('Tbl_Ticker', 'db_ticker');
define('Tbl_Admin', 'db_admin');
define('Tbl_Leads', 'db_leads');
define('Tbl_LeadAssign', 'db_lead_assign');
define('Tbl_Pending', 'db_pending_action');
define('Tbl_Reminder', 'db_reminder_scheduler');
define('Tbl_Log', 'uploaded_leads_log');
define('Tbl_processing_center', 'processing_center_mapping');
define('Tbl_LoginLog', 'db_app_login_logs');
define('Tbl_SmsAuth', 'db_sms_credentials');
define('Tbl_Notification', 'db_notification');

define('EXCEL_ALPHA', serialize(array(
    '0'=>'A',
    '1'=>'B',
    '2'=>'C',
    '3'=>'D',
    '4'=>'E',
    '5'=>'F',
    '6'=>'G',
    '7'=>'H',
    '8'=>'I',
    '9'=>'J',
    '10'=>'K',
    '11'=>'L',
    '12'=>'M',
    '13'=>'N',
    '14'=>'O',
    '15'=>'P',
    '16'=>'Q',
    '17'=>'R',
    '18'=>'S',
    '19'=>'T',
    '20'=>'U',
    '21'=>'V',
    '22'=>'W',
    '23'=>'X',
    '24'=>'Y',
    '25'=>'Z',
    '26'=>'AA',
    '27'=>'AB',
    '28'=>'AC',
    '29'=>'AD',
    '30'=>'AE',
    '31'=>'AF',
    '32'=>'AG',
    '33'=>'AH',
    '34'=>'AI',
    '35'=>'AJ',
    '36'=>'AK',
    '37'=>'AL',
    '38'=>'AM',
    '39'=>'AN',
    '40'=>'AO',
    '41'=>'AP',
    '42'=>'AQ',
    '43'=>'AR',
    '44'=>'AS',
    '45'=>'AT',
    '46'=>'AU',
    '47'=>'AV',
    '48'=>'AW',
    '49'=>'AX',
    '50'=>'AY',
    '51'=>'AZ',
    '52'=>'BA',
    '53'=>'BB',
    '54'=>'BC',
    '55'=>'BD',
    '56'=>'BE',
    '57'=>'BF',
    '58'=>'BG',
    '59'=>'BH',
    '60'=>'BI',
    '61'=>'BJ',
    '62'=>'BK',
    '63'=>'BL',
)));
