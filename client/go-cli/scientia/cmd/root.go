package cmd

import (
	"errors"
	"fmt"
	"github.com/adrg/xdg"
	"github.com/spf13/cobra"
	"gopkg.in/yaml.v3"
	"os"
	Helper "scientia/lib"
)

/**
 * scientia
 *
 * Copyright 2023 - 2024 Johannes Keßler
 *
 * https://www.bananas-playground.net/projekt/scientia/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl-3.0.
 */

// FlagVerbose is a global flag
var FlagVerbose bool

// FlagDebug is a global flag
var FlagDebug bool

// ConfigStruct file struct
type ConfigStruct struct {
	Endpoint struct {
		Add   string `yaml:"add"`
		Get   string `yaml:"get"`
		Secret string `yaml:"secret"`
	} `yaml:"endpoint"`
}

// GetResponse struct for the get.php request
type GetResponse struct {
	Data []GetResponseEntry `json:"data"`
	Status int `json:"status"`
	Message string `json:"message"`
}
// GetResponseEntry struct is the entry itself
type GetResponseEntry struct {
	Ident string `json:"ident"`
	Date string `json:"date"`
	Body string `json:"body"`
}

// The ScientiaConfig used globally
var ScientiaConfig ConfigStruct

var ScientiaConfigPath = xdg.ConfigHome + "/scientia"
var ScientiaConfigFile = ScientiaConfigPath + "/scientia.yaml"

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
	rootCmd.PersistentFlags().BoolVar(&FlagVerbose, "verbose", false, "Add verbose output")
	rootCmd.PersistentFlags().BoolVar(&FlagDebug, "debug", false, "Add debug output")

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

	if _, err := os.Stat(ScientiaConfigFile); errors.Is(err, os.ErrNotExist) {
		fmt.Println("Warning: No config file found!")
		return
	}

	existingConfigFile, err := os.Open(ScientiaConfigFile)
	Helper.ErrorCheck(err, "Can not open config file. Did you create one?")
	defer existingConfigFile.Close()

	var decoder = yaml.NewDecoder(existingConfigFile)
	err = decoder.Decode(&ScientiaConfig)
	Helper.ErrorCheck(err, "Can not decode config file")

	if ScientiaConfig.Endpoint.Add == "" || ScientiaConfig.Endpoint.Get == "" || ScientiaConfig.Endpoint.Secret == "" {
		fmt.Println("WARNING Empty or outdated config?")
	}

	if FlagDebug {
		fmt.Println("DEBUG Add endpoint: " + ScientiaConfig.Endpoint.Add)
		fmt.Println("DEBUG Get endpoint: " + ScientiaConfig.Endpoint.Get)
		fmt.Println("DEBUG Secret: " + ScientiaConfig.Endpoint.Secret)
	}
}
