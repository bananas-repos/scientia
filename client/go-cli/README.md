# scienta go cli client

This is a terminal cli client written in go to be used with scientia
https://://www.bananas-playground.net/projekt/scientia/

!WARNING!
This is a very simple, with limited experience written, go program.
Use at own risk and feel free to improve.

Currently only tested on linux.

# Howto build

Nothing special, just use the provided Makefile or directly `go build -o scientia` to use your current os/arch settings.

Or use the Makefile with just `make` to build the binary. 

# Usage

At first usage you need to create the config and the individual secret.
Run `$ scientia config init` to create the default config file.
The path to the config file is printed. Use `scientia config edit` to edit at least the host setting.
Update your server it with the secret, which is randomly created.

## Create

Read from a file `$ scientia file.txt` or piped `$ cat file.txt | scientia-cli`

# Commandline arguments

```
Usage:
  scientia [flags]
  scientia [command]

Available Commands:
  config      Modify config
  help        Help about any command

Flags:
  -d, --debug     debug output
  -h, --help      help for scientia
  -v, --verbose   verbose output

Use "scientia [command] --help" for more information about a command.
```
