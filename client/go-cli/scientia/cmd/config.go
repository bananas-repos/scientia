package cmd

import (
	"os"

	"github.com/spf13/cobra"
)

var configCmd = &cobra.Command {
	Use: "config",
	Short: "Modify config",
	Long: "Read, edit and initialize scientia configuration.",
	Run: func(cmd *cobra.Command, args []string) {
		if len(args) == 0 {
			cmd.Help()
			os.Exit(0)
		}
	},
}

func init() {
	rootCmd.AddCommand(configCmd)
}
