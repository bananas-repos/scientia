package cmd

import (
	"fmt"
	"github.com/kirsle/configdir"
	"github.com/spf13/cobra"
	"gopkg.in/yaml.v3"
	"log"
	"os"
	Helper "scientia/lib"
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
	rootCmd.PersistentFlags().BoolVar(&FlagVerbose, "verbose", false, "verbose output")
	rootCmd.PersistentFlags().BoolVar(&FlagDebug, "debug", false, "debug output")

	cobra.OnInitialize(loadConfig)
}

func Execute() {
	if err := rootCmd.Execute(); err != nil {
		fmt.Fprintln(os.Stderr, err)
		os.Exit(1)
	}
}

// Read and make sure the basics are in the config
func loadConfig() {
	if FlagDebug {
		fmt.Println("DEBUG using config file: " + ScientiaConfigFile)
	}
	existingConfigFile, err := os.Open(ScientiaConfigFile)
	Helper.ErrorCheck(err, "Can not open config file. Did you create one?")
	defer existingConfigFile.Close()

	var decoder = yaml.NewDecoder(existingConfigFile)
	err = decoder.Decode(&ScientiaConfig)
	Helper.ErrorCheck(err, "Can not decode config file")

	if ScientiaConfig.Endpoint.Host == "" || ScientiaConfig.Endpoint.Secret == "" {
		log.Fatal("Empty config?")
	}

	if FlagDebug {
		fmt.Println("DEBUG Endpoint: " + ScientiaConfig.Endpoint.Host)
		fmt.Println("DEBUG Secret: " + ScientiaConfig.Endpoint.Secret)
	}
}
