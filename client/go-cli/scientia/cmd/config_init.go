package cmd

import (
	"bufio"
	"errors"
	"fmt"
	"os"

	Helper "scientia/lib"

	"github.com/kirsle/configdir"
	"github.com/spf13/cobra"
	"gopkg.in/yaml.v3"
)

/**
 * scientia
 *
 * Copyright 2023 - 2024 Johannes Keßler
 *
 * https://www.bananas-playground.net/projekt/scientia/
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the COMMON DEVELOPMENT AND DISTRIBUTION LICENSE
 *
 * You should have received a copy of the
 * COMMON DEVELOPMENT AND DISTRIBUTION LICENSE (CDDL) Version 1.0
 * along with this program.  If not, see http://www.sun.com/cddl/cddl.html
 */

var configPath = configdir.LocalConfig("scientia")
var configFile = configPath + "/.scientia.yaml"

func init() {
	configCmd.AddCommand(configInitCmd)
	configCmd.AddCommand(configReadCmd)
}

// INIT config file

var configInitCmd = &cobra.Command {
	Use:   "init",
	Short: "Initialize config",
	Long:  `Read, edit and initialize scientia configuration`,
	Run: func(cmd *cobra.Command, args []string) {
		initConfig()
	},
}

// initConfig which creates the default config file
func initConfig() {
	err := configdir.MakePath(configPath) // Ensure it exists.
	Helper.ErrorCheck(err, "No $HOME/.config/scientia directory available?")

	if FlagDebug {
		fmt.Printf("DEBUG Local user config path: %s\n", configPath)
		fmt.Printf("DEBUG Local user config file: %s\n", configFile)
	}

	if _, err := os.Stat(configFile); errors.Is(err, os.ErrNotExist) {
		fmt.Printf("Creating new default config file: %s \n", configFile)

		newConfig, err := os.Create(configFile)
		Helper.ErrorCheck(err, "Can not create config file!")
		defer newConfig.Close()

		// yaml package can not write comments yet, so creating it manually
		_, err = fmt.Fprintf(newConfig, "# scientia go client config file.\n")
		Helper.ErrorCheck(err, "Can not write to new config file")
		fmt.Fprintf(newConfig, "# See %s for more details.\n", Helper.Website)
		fmt.Fprintf(newConfig, "# Version: %s\n", Helper.Version)
		fmt.Fprintf(newConfig, "endpoint:\n")
		fmt.Fprintf(newConfig, "  host: http://your-scientia-endpoi.nt/api.php\n")
		fmt.Fprintf(newConfig, "  secret: %s\n", Helper.RandStringBytes(50))
	} else {
		fmt.Printf("Config file exists.: %s \n", configFile)
		fmt.Println("Use 'read' to display or 'edit' to modify the config file.")
	}
}

// READ config file

var configReadCmd = &cobra.Command {
	Use:   "read",
	Short: "Read config file",
	Long:  "Read the config file and print it to stdout",
	Run: func(cmd *cobra.Command, args []string) {
		readConfig()
	},
}

// readConfig does read the existing config file and prints it contents and validates the yaml.
func readConfig() {
	if FlagDebug {
		fmt.Printf("DEBUG Local user config path: %s\n", configPath)
		fmt.Printf("DEBUG Local user config file: %s\n", configFile)
	}

	existingConfigFile, err := os.Open(configFile)
	Helper.ErrorCheck(err, "Can not open config file. Did you create one with 'config init'?")
	defer existingConfigFile.Close()

	if FlagVerbose {
		fmt.Printf("Reading config file: %s \n", configFile)
	}

	// make sure it can be parsed and thus it is valid
	var decoder = yaml.NewDecoder(existingConfigFile)
	err = decoder.Decode(&ScientiaConfig)
	Helper.ErrorCheck(err, "Can not parse config file")

	// just display the contents
	existingConfigFile.Seek(0,0) // reset needed
	configBuffer := bufio.NewReader(existingConfigFile)
	for {
		line, _, err := configBuffer.ReadLine()
		if len(line) > 0 {
			fmt.Println(string(line))
		}
		if err != nil {
			break
		}
	}
}
