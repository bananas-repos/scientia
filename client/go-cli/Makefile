all:
	@echo "Options are: build scientia-cli scientia-cli-linux-amd64 scientia-cli-windows-amd64 scientia-cli-darwin-amd64 clean buildall"

scientia-cli:
	@echo "Building for local os/arch..."
	go build -o scientia-cli

scientia-cli-linux-amd64:
	@echo "Building for linux/amd64 arch..."
	GOOS=linux GOARCH=amd64 go build -o scientia-cli-linux-amd64

scientia-cli-windows-amd64:
	@echo "Building for windows/amd64 arch..."
	GOOS=windows GOARCH=amd64 go build -o scientia-cli-windows-amd64

scientia-cli-darwin-amd64:
	@echo "Building for macOS/amd64 arch..."
	GOOS=darwin GOARCH=amd64 go build -o scientia-cli-darwin-amd64

build:
	@echo "Building for local os/arch..."
	go build -o scientia-cli-`go env GOOS`-`go env GOARCH`

buildall: scientia-cli-linux-amd64 scientia-cli-windows-amd64 scientia-cli-darwin-amd64
	@echo "Building for linux/amd64, windows/amd64 and macOS/amd64 arch..."

clean:
	rm -f scientia-cli scientia-cli-darwin-amd64 scientia-cli-windows-amd64 scientia-cli-linux-amd64