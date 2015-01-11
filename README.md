MinecraftControlPanel
=====================

Minecraft Control Panel written in PHP, used as a web management interface full of rich features!

======
Status
======
Minecraft Control Panel is currently in beta. It does not have an installer currently that is in the works, but for now here's a rough guide how to install it

=============
Prerequisites
=============
A *NIX Machine, with an Apache or Nginx webserver running with PHP 5 or later, must have mysql(PDO) and the libssh-php-5 addons installed on it.

1. Clone this repo to the webdir root
2. Edit the settings file under php/includes/
3. Chanage the lines to your environment settings
4. Create database from the sql file in the repo 
5. Generate your own RSA Keys and put them in a directory accessable by the www user and set the path in the database table "cpanel_settings".
6. Add your first user to the "cpanel_users" table with the script labelled "first_user.php" and add the chosen password to the first variable
7. Run the script and copy/paste the base64_encoded string into the password field when adding the account to the database
8. Login to the Control Panel with your credentials. Enjoy!

========
Features
========

1. Remote server management via ssh from host installed with Control Panel
2. Remote plugin installation on server
3. Backup creation and download
4. Start/Stop Server remotely!
5. Manage dedicated server hosts to create many servers on

==========
To-Do List
==========
1. Installer
2. Add servers to the panel remotely
3. Remove servers remotely
4. Add new users + Manage user accounts
5. Various other features I haven't thought of yet xD
