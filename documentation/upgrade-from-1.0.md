# Config changes

Update your existing `config.php` file with the following changes.
Missing those changes, errors will happen.

# i18n
New config for i18n. Open config.php and add the following:
```
# language settings
const FRONTEND_LANGUAGE = 'en';
```
currently only en (default) and de are available.

#  Installation URL

To provide the correct URL please add this.

```
# Installation Domain. Webrootpath will be added automatically
const INSTALL_URL = 'http://localhost';
```
