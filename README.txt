
















-------------Developers

Software Installation:
1.Apache web server
2.Enable/install php on top of web server
3.Install MySQL
4.Install phpmyadmin & configure to connect to MySQL
5.Install "Eclipse for PHP Developers" (Make sure NOT for Java developers)



Download the attached 2 files.
1.eclipse project file (.phar)
2."mycompany" database export (.sql)



Steps to start working:
1.Launch Eclipse, create a php project as "php-stockinv" with "Create new project in Workspace" option. Then select the project & import project file.
2.login to MySQL using phpmyadmin. Get acquainted with it.
3.Create a database with name "mycompany" & a user "km" (pwd: Asdf@1234) If lazy, click on SQL on home & execute contents of the db_user.sql file from PHP project.
3.Go to "mycompany" db, then click "import" on phpmyadmin. Then use the database export file to import data & follow instructions.
4.Setup to test php site in either way.
Unix: create a shortcut for project if files are in eclipse workspace using command:
              sudo ln -s /home/kiran/workspace_php/mycompany /var/www/html/mycompany
Windows: Check if you can copy the files onto web server & configure eclipse directly to that location.

---------------
