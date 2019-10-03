# roistat2sql
PHP script for import data from roistat analytics to medialog ERP mssql database

## installation
* install Microsoft Drivers 4.0 for PHP for SQL Server
* Microsoft ODBC Driver 11 for SQL Server
* Microsoft Visual C++ 2012 Redistributable
* add extensions in php.ini for you version
```
extension=php_pdo_sqlsrv_56_ts.dll
extension=php_sqlsrv_56_ts.dll
```

* create tables with US_WEB_ROISTAT_medialog.sql
* check you settings in config.php
