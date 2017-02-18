MinecraftControlPanel
=====================

Minecraft Control Panel written in PHP, used as a web management interface full of rich features!

======
Status
======
MCP is WIP at the moment, servers can be created if you create a host, then add a server on a host

=============
Prerequisites
=============
A *NIX Machine, with an Apache or Nginx webserver running with PHP 5 or later, must have mysql(PDO) and the libssh-php-5 (ssh) addons installed on it. As well as this have `npm` installed; have `bower` as a global module as this project uses it.

1. Install Apache2/Nginx, PHP and addons, npm and bower to machine
2. Clone repo into www root
3. Run `npm install` - bower install will be run after as a postinstall script
4. Use the `cpanel_setup.sql` file to insert tables into database (this is temporary whilst its WIP; use phpmyadmin or equiv. to make it easier)

Note - Passwords are bcyrpt hashes, the default in the `cpanel.sql` file is `password`. With the username being `admin`

5. Adjust the config file in WWW ROOT `/app/core/config.php`
6. Adjust the token secret for JWT
7. Enjoy the panel, I will be updating this project weekly, I've got exams at the moment.

Thank You!

========
Features
========

1. Remote server management via ssh from host installed with Control Panel
2. Start/Stop Server remotely!
3. Manage dedicated server hosts to create many servers on

==========
To-Do List
==========
1. Installer
2. Add servers to the panel remotely
3. Remove servers remotely
4. Add new users + Manage user accounts
