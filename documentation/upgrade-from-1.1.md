# Config changes

Update your existing `config.php` file with the following changes.
Missing those changes, errors will happen. See `config.php.default` for defaults.

## Constant rename

Rename `PATH_SYSTEMOUT` to `PATH_LOG`.

## Constant addition

Add the following line to your config file: `const LOGFILE = PATH_LOG.'/scientia.log';`

## Log directory rename

Rename the directory `systemout` to `log`.

# Database changes

Run the following SQL to alter your existing database.

```
ALTER TABLE `sc_entry` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
ALTER TABLE `sc_entry` CHANGE `ident` `ident` CHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_bin NOT NULL;
ALTER TABLE `sc_entry` CHANGE `words` `words` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;
ALTER TABLE `sc_entry` CHANGE `body` `body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;
```
