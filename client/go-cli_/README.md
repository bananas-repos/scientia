# scienta go cli client

This is a terminal cli client written in go to be used with scientia
https://://www.bananas-playground.net/projekt/scientia/

!WARNING!
This is a very simple, with limited experience written, go program.
Use at own risk and feel free to improve.

# Howto build

Nothing special, just use the provided Makefile or directly 
"go build -o scientia-cli" to use your current os/arch settings.

# Usage

At first usage you need to create the config and the individual secret.
Run $scientia-cli -create-config-file to create the default config file.
The path to the config file is printed.
Change the host address and update your server it with the secret, which is randomly created.

## Create

Read from a file `$ scientia-cli file.txt` or piped `$ cat file.txt | scientia-cli`

# Commandline arguments

## Optinal

+ `-create-config-file` Create default config file
+ `-debug` Print debug infos
+ `-verbose` Produce verbose output