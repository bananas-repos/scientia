# Check the requirements

Make sure to meet those requirement to avoid any bugs.

# Unpack

Unpack the release to a temporary folder.

# MySQL

Create a database or use an existing one (a table prefix will be used) and write down the credentials.

# Create DB table

Open `scientia.sql.txt` file and run the given SQL command in the database you are using.
This command creates the needed tables. A Database will not be created.

# Config

Copy `config.php.default` to `config.php` and open it.
Input your MySQL credentials and update the other settings there.

# Upload

Upload the contents of the webroot from your temp folder onto your webserver.
Make sure the path matches the setting from the config.

# Directory permissions

Make sure the `systemout` directory has write permission to the webserver user.
