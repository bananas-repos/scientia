# Determine this makefile's path.
# Be sure to place this BEFORE `include` directives, if any.
THIS_FILE := $(lastword $(MAKEFILE_LIST))

all:
	@echo "Options are: build and buildall"

build:
	@echo "Building for local os/arch..."
	go build -o scientia-cli-`go env GOOS`-`go env GOARCH`

buildall:
	@echo "Building for linux/amd64 arch..."
	GOOS=linux GOARCH=amd64 go build -o scientia-cli-linux-amd64
	@echo "Done: scientia-cli-linux-amd64"
	@echo "Building for windows/amd64 arch..."
	GOOS=windows GOARCH=amd64 go build -o scientia-cli-windows-amd64
	@echo "Done: scientia-cli-windows-amd64"
	@echo "Building for macOS/amd64 arch..."
	GOOS=darwin GOARCH=amd64 go build -o scientia-cli-darwin-amd64
	@echo "Done: scientia-cli-darwin-amd64"