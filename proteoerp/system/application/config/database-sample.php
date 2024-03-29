<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = "default";
$active_record = TRUE;

$db['default']['hostname'] = "localhost";
$db['default']['username'] = "usuario";
$db['default']['password'] = "";
$db['default']['database'] = "BaseDato";
$db['default']['dbdriver'] = "mysqli";
$db['default']['dbprefix'] = "";
$db['default']['pconnect'] = false;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = "";
$db['default']['char_set'] = "latin1";
$db['default']['dbcollat'] = "latin1_swedish_ci";

//$db['default']['char_set'] = "utf8";
//$db['default']['dbcollat'] = "utf8_general_ci";

/*
Deben ser definidas las bases de datos locales
que pertenecen a las sucursales (si las tiene)
sucuX  X=prefijo en la tabla sucu

$db['sucuX']['hostname'] = "localhost";
$db['sucuX']['username'] = "usuario";
$db['sucuX']['password'] = "";
$db['sucuX']['database'] = "nombre";
$db['sucuX']['dbdriver'] = "mysql";
$db['sucuX']['dbprefix'] = "";
$db['sucuX']['pconnect'] = false;
$db['sucuX']['db_debug'] = TRUE;
$db['sucuX']['cache_on'] = FALSE;
$db['sucuX']['cachedir'] = "";
$db['sucuX']['char_set'] = "latin1";
$db['sucuX']['dbcollat'] = "latin1_swedish_ci";
*/

/* Conecciones a otros sistema de la familia Proteo

$db['farmax']['hostname'] = "localhost";
$db['farmax']['username'] = "usuario";
$db['farmax']['password'] = "";
$db['farmax']['database'] = "droguerias";
$db['farmax']['dbdriver'] = "mysql";
$db['farmax']['dbprefix'] = "";
$db['farmax']['pconnect'] = false;
$db['farmax']['db_debug'] = TRUE;
$db['farmax']['cache_on'] = FALSE;
$db['farmax']['cachedir'] = "";
$db['farmax']['char_set'] = "latin1";
$db['farmax']['dbcollat'] = "latin1_swedish_ci";


*/
$db['olap']['hostname'] = "localhost";
$db['olap']['username'] = "usuario";
$db['olap']['password'] = "";
$db['olap']['database'] = "olap";
$db['olap']['dbdriver'] = "mysql";
$db['olap']['dbprefix'] = "";
$db['olap']['pconnect'] = false;
$db['olap']['db_debug'] = TRUE;
$db['olap']['cache_on'] = FALSE;
$db['olap']['cachedir'] = "";
$db['olap']['char_set'] = "latin1";
$db['olap']['dbcollat'] = "latin1_swedish_ci";


/*
$db['default']['char_set'] = "latin2";
$db['default']['dbcollat'] = "latin2_general_ci";
*/
?>