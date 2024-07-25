package cmd

import (
	"bufio"
	"fmt"
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
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the COMMON DEVELOPMENT AND DISTRIBUTION LICENSE
 *
 * You should have received a copy of the
 * COMMON DEVELOPMENT AND DISTRIBUTION LICENSE (CDDL) Version 1.0
 * along with this program.  If not, see http://www.sun.com/cddl/cddl.html
 */

// Subcommand of config
// to read the config file

func init() {
	configCmd.AddCommand(configReadCmd)
}

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
		fmt.Printf("DEBUG Local user config path: %s\n", ScientiaConfigPath)
		fmt.Printf("DEBUG Local user config file: %s\n", ScientiaConfigFile)
	}

	existingConfigFile, err := os.Open(ScientiaConfigFile)
	Helper.ErrorCheck(err, "Can not open config file. Did you create one with 'config init'?")
	defer existingConfigFile.Close()

	if FlagVerbose {
		fmt.Printf("Reading config file: %s \n", ScientiaConfigFile)
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
