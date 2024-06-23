package cmd

import (
	"fmt"
	"github.com/kirsle/configdir"
	"github.com/spf13/cobra"
	"os"
)

// FlagVerbose is a global flag
var FlagVerbose bool

// FlagDebug is a global flag
var FlagDebug bool

// ConfigStruct file struct
type ConfigStruct struct {
	Endpoint struct {
		Host   string `yaml:"host"`
		Secret string `yaml:"secret"`
	} `yaml:"endpoint"`
}

// The ScientiaConfig used globally
var ScientiaConfig ConfigStruct

var ScientiaConfigPath = configdir.LocalConfig("scientia")
var ScientiaConfigFile = ScientiaConfigPath + "/.scientia.yaml"

// The rootCmd
var rootCmd = &cobra.Command{
	Use:   "scientia",
	Short: "scientia client",
	Long: `cognizance, insight, knowledge.
A client to scientia.
More information: https://www.bananas-playground.net/projekt/scientia/`,
	Run: func(cmd *cobra.Command, args []string) {
		// display help if no arguments are given
		if len(args) == 0 {
			cmd.Help()
			os.Exit(0)
		}
	},
}

func init() {
	rootCmd.CompletionOptions.DisableDefaultCmd = true
	// add global flags
	rootCmd.PersistentFlags().BoolVarP(&FlagVerbose, "verbose", "v", false, "verbose output")
	rootCmd.PersistentFlags().BoolVarP(&FlagDebug, "debug", "d", false, "debug output")
}

func Execute() {
	if err := rootCmd.Execute(); err != nil {
		fmt.Fprintln(os.Stderr, err)
		os.Exit(1)
	}
}
