package cmd

import (
	"errors"
	"fmt"
	"os"

	"github.com/kirsle/configdir"
	"github.com/spf13/cobra"

	Helper "scientia/lib"
)

var configInitCmd = &cobra.Command {
	Use: "init",
	Short: "Initialize config",
	Long: `Read, edit and initialize scientia configuration`,
	Run: func(cmd *cobra.Command, args []string) {
		initConfig()
	},
}

func init() {
	configCmd.AddCommand(configInitCmd)
}


func initConfig() {
	configPath := configdir.LocalConfig("scientia")
	err := configdir.MakePath(configPath) // Ensure it exists.
	Helper.ErrorCheck(err, "No $HOME/.config/scientia directory available?")
	var configFile = configPath + "/.scientia.yaml"

	if FlagDebug {
		fmt.Printf("Local user config path: %s\n", configPath)
		fmt.Printf("Local user config file: %s\n", configFile)
	}

	if _, err := os.Stat(configFile); errors.Is(err, os.ErrNotExist) {
		fmt.Printf("Creating new default config file: %s \n", configFile)

		newConfig, err := os.Create(configFile)
		Helper.ErrorCheck(err, "Can not create config file!")
		defer newConfig.Close()

		_, err = fmt.Fprintf(newConfig, "# scientia go client config file.\n")
		Helper.ErrorCheck(err, "Can not write to new config file")
		fmt.Fprintf(newConfig, "# See %s for more details.\n", Helper.Website)
		fmt.Fprintf(newConfig, "# Version: %s\n", Helper.Version)
		fmt.Fprintf(newConfig, "endpoint:\n")
		fmt.Fprintf(newConfig, "  host: http://your-scientia-endpoi.nt/api.php\n")
		fmt.Fprintf(newConfig, "  secret: %s\n", Helper.RandStringBytes(50))

	} else {
		fmt.Printf("Config file exists.: %s \n", configFile)
		fmt.Println("User read to display or edit to modify the config file.")
	}
}
