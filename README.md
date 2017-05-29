# == Yehoodi.com Website ==

# == Vagrant Box SetUp ==

## Requirements:

Note: You will have to restart your computer after installing VirtualBox and Vagrant!

Please download the latest version of VirtualBox by Oracle:

https://www.virtualbox.org/wiki/Downloads

Also, install vagrant on your host machine:

http://www.vagrantup.com/

You may want to get familiar with VirtualBox (install a box, play around for a bit) and
Vagrant (read the [getting started guide][1]) before continuing to be familiar with what
is going on here. RTFM!


##Preparation (In the host machine):
Before doing any vagrant stuff, you may want to add an entry in your hosts file 
so you can use a friendly URL as opposed to an IP address. The vagrant box will
use the IP of 192.168.56.101. So you may want to add the following:

Mac hosts file is at /etc/hosts

> 192.168.56.101	dev.yehoodi_dev.local

## Downloading application code and vagrant box:
Mac users open your terminal for this next part. Windows users, try Git Bash.

Create or move into the directory where you will keep the Yehoodi application code.
```
> mkdir yehoodi_dev
> cd yehoodi_dev
```
Get the latest code from Yehoodi github
```
> git clone https://github.com/Yehoodi/yehoodi3.git .
```
Get a copy of the database. Use the shared file that Spuds probably gave you and unzip it into the
/sql directory of your local site. It should be /sql/datyehoodi3.sql when it's copied. The vagrant will
need this file to be there when it builds your dev box.

Now start the initial vagrant box
```
> vagrant up
```
At this point your local dev vm will be set up. Go get a cup of coffee. If you are
on Mac or Linux, you may be asked for your administrator password for your HOST machine. 
This is used to mount the NFS fileshare within the host. Enter your password so the 
install can continue.

When done we need to ssh into the vagrant box and do some final things manually
```
> vagrant ssh
```
## One-time set-up script
We need to run a shell script to copy over important files. This only needs to be done once after you
first create your vm.
```
$ /var/www/required/required.sh
```

# Setting up MySQL (In the vagrant box):
Now we need to set permissions on the local MySQL server in the vagrant box.

```
$ mysql -u root --password=yehoodi
mysql> GRANT ALL ON *.* TO 'root'@'%';
mysql> exit;
```

(Note: Do NOT do this on a production machine as it is extremely insecure!)

Now restart services
```
$ sudo service mysql restart
$ sudo service nginx restart
$ sudo service php5-fpm restart
```
You can access the MySQL instance from your HOST machine. Load your favorite database 
utility ([Sequel Pro][2]/ [MySQL Workbench][3], etc) and connect to the vagrant box with the following...

```
Name: Yehoodi
Host: 192.168.56.101
Username: root
Password: [leave this blank]
Port: 3306
```
## Browse the Yehoodi site (From the host machine):

At this point you can either use the I.P. address or url (if you updated your hosts
file) to get to the Yehoodi local site server from the vagrant box.

> Go to http://dev.yehoodi_dev.local/

Cheers!

[1]: http://docs.vagrantup.com/v2/getting-started/index.html
[2]: http://www.sequelpro.com/
[3]: http://dev.mysql.com/downloads/tools/workbench/
