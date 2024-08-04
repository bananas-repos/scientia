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
