all: scientia

scientia:
	@echo "Building for local os/arch..."
	go build -o scientia
	chmod u+x scientia

devbuild: clean scientia

clean:
	rm -f scientia
