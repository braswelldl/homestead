<?php

define('HMS_DEBUG', false);

/**************
 * Login Link *
 **************/
define('LOGIN_TEST_FLAG', 'false');
define('HMS_LOGIN_LINK', 'secure');

/**********************
 * SOAP Testing Flags *
 **********************/

/**
 * SOAP Info test flag
 * Set to true to use canned student info (no SOAP connection
 * will ever be made).
 */
define('SOAP_INFO_TEST_FLAG', false);

/**
 * Name of the SOAP override file. Only used if SOAP_INFO_TEST_FLAG is true
 */
//define('SOAP_OVERRIDE_FILE', 'FakeSoapTable.php');
define('SOAP_OVERRIDE_FILE', 'TestSOAP.php');


/**
 * WSDL File Path
 * If the SOAP_INFO_TEST_FLAG above is FALSE,
 * then this is the location of the WSDL file
 * we'll try to use. It is relative to the
 * phpWebsite installation directory.
 */
define('WSDL_FILE_PATH', 'inc/shs0001.wsdl.prod');

/**
 * SOAP Data Override Class Path
 * Path to the SOAPDataOverride class. This
 * class allows you to override data for
 * students.
 */
define('SOAP_DATA_OVERRIDE_PATH', 'inc/SOAPDataOverride.php');

/**
 * WSDL File Name
 * If the SOAP_INFO_TEST_FLAG above is FALSE,
 * then this is the WSDL file we'll try to use
 * to contact a web server somewhere.
 * @deprecated in favor of WSDL_FILE_PATH
 */
//define('WSDL_FILE_NAME', 'shs0001.wsdl.prod');

/**
 * SOAP Reporting test flag
 * Set to true to prevent applications, assignments, etc.
 * from being reported back to banner.
 */
define('SOAP_REPORT_TEST_FLAG', false);

/**
 * Email testing flag
 * Set to true to prevent email from actually being sent.
 * Instead, it will be logged to a text file.
 */
define('EMAIL_TEST_FLAG', false);

/* Errors */
//ini_set('ERROR_REPORTING', E_ALL);
//ini_set('display_errors', 1);

/* Memory limit */
ini_set('memory_limit', '512M');

define('WKPDF_PATH', PHPWS_SOURCE_DIR . 'mod/hms/vendor/ioki/wkhtmltopdf-amd64-centos6/bin/');

define('USE_XVFB', true);
define('XVFB_PATH', '/usr/bin/xvfb-run');

