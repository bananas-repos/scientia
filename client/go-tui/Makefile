all:
	@echo "Options are: build and buildall"

build:
	@echo "Building for local os/arch..."
	go build -o scientia-tui-`go env GOOS`-`go env GOARCH`

buildall:
	@echo "Building for linux/amd64 arch..."
	GOOS=linux GOARCH=amd64 go build -o scientia-tui-linux-amd64
	@echo "Done: scientia-tui-linux-amd64"
	@echo "Building for windows/amd64 arch..."
	GOOS=windows GOARCH=amd64 go build -o scientia-tui-windows-amd64
	@echo "Done: scientia-tui-windows-amd64"
	@echo "Building for macOS/amd64 arch..."
	GOOS=darwin GOARCH=amd64 go build -o scientia-tui-darwin-amd64
	@echo "Done: scientia-tui-darwin-amd64"