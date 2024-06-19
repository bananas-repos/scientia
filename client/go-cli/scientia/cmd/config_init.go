package cmd

import (
	"errors"
	"fmt"
	"os"

	"github.com/kirsle/configdir"
	"github.com/spf13/cobra"
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

func init() {
	configCmd.AddCommand(configInitCmd)
	configCmd.AddCommand(configReadCmd)
}

// The Config file struct
type Config struct {
	Endpoint struct {
		Host   string `yaml:"host"`
		Secret string `yaml:"secret"`
	} `yaml:"endpoint"`
}

var configInitCmd = &cobra.Command{
	Use:   "init",
	Short: "Initialize config",
	Long:  `Read, edit and initialize scientia configuration`,
	Run: func(cmd *cobra.Command, args []string) {
		initConfig()
	},
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

var configReadCmd = &cobra.Command{
	Use:   "read",
	Short: "Read config file",
	Long:  "Read the config file and print it to stdout",
	Run: func(cmd *cobra.Command, args []string) {
		readConfig()
	},
}

func readConfig() {

}
