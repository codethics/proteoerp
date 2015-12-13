ProteoERP es un un software hecho en PHP que le permite a una empresa, automatizar e integrar procesos de gestión administrativa, presupuestaria, contabilidad, inventarios, facturación, gastos, acceder a la información en tiempo real.


## Instalación ##

La instalación se hace desde el svn.

  * Crear una carpeta de nombre "proteoerp" dentro del directrio de Apache (En OpenSuSE es "srv/www/htdocs").
  * Situarse dentro de la carpeta recién creada y escribir "**`svn checkout http://proteoerp.googlecode.com/svn/trunk/proteoerp . `**" (**notese el punto al final**), esto creara una copia de solo lectura, en caso de ser una copia de trabajo se debe escribir esto "**`svn checkout https://proteoerp.googlecode.com/svn/trunk/proteoerp . --username <#usuario#>`**".
  * Renombrar el archivo **.htaccess-sample** a **.htaccess**, también con los archivos **config-sample.php** y **database-sample.php** a **config.php** y **database.php** respectivamente ubicados en "system/applications/config".
  * Editar el archvo **database.php** para colocar los parámetros correctos para conectarse a la base de datos.

## Actualización ##

Para actualizar basta con colocarse en el directorio de proteoerp y escribir "**`svn up`**"


Cualquier duda mandar un correo a aahahocevar :arroba: gmail.com