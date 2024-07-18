package cmd

import (
	"fmt"
	"github.com/spf13/cobra"
	"io"
	"os"
	Helper "scientia/lib"
)

func init() {
	rootCmd.AddCommand(pullCmd)
}

var pullCmd = &cobra.Command {
	Use: "pull",
	Short: "Download",
	Long: "Add a new entry based on a file or piped cat file | scientia add. Returns the url to the new entry.",
	Run: func(cmd *cobra.Command, args []string) {
		// check if there is a file or piped content
		var inputString string

		if len(args) == 1 {
			if FlagVerbose {
				fmt.Println("Read from file argument")
			}
			bytes, err := os.ReadFile(args[0])
			Helper.ErrorCheck(err, "Error opening file")
			inputString = string(bytes)
		} else if stat, _ := os.Stdin.Stat(); (stat.Mode() & os.ModeCharDevice) == 0  {
			if FlagVerbose {
				fmt.Println("Read from piped stdin")
			}
			bytes, _ := io.ReadAll(os.Stdin)
			inputString = string(bytes)
		} else {
			cmd.Help()
			os.Exit(0)
		}

		response := upload(inputString)

		// print the result and link to the pasty
		fmt.Printf("Status: %d\n", response.Status)
		fmt.Printf("Message: %s\n", response.Message)
	},
}
