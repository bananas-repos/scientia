package cmd

import (
	"errors"
	"fmt"
	"github.com/kirsle/configdir"
	"github.com/spf13/cobra"
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


func init() {
	configCmd.AddCommand(configInitCmd)
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
	err := configdir.MakePath(ScientiaConfigPath) // Ensure it exists.
	Helper.ErrorCheck(err, "No $HOME/.config/scientia directory available?")

	if FlagDebug {
		fmt.Printf("DEBUG Local user config path: %s\n", ScientiaConfigPath)
		fmt.Printf("DEBUG Local user config file: %s\n", ScientiaConfigPath)
	}

	if _, err := os.Stat(ScientiaConfigFile); errors.Is(err, os.ErrNotExist) {
		fmt.Printf("Creating new default config file: %s \n", ScientiaConfigFile)

		newConfig, err := os.Create(ScientiaConfigFile)
		Helper.ErrorCheck(err, "Can not create config file!")
		defer newConfig.Close()

		// yaml package can not write comments yet, so creating it manually
		_, err = fmt.Fprintf(newConfig, "# scientia go client config file.\n")
		Helper.ErrorCheck(err, "Can not write to new config file")
		fmt.Fprintf(newConfig, "# See %s for more details.\n", Helper.Website)
		fmt.Fprintf(newConfig, "# Version: %s\n", Helper.Version)
		fmt.Fprintf(newConfig, "endpoint:\n")
		fmt.Fprintf(newConfig, "  url: \"http://your-scientia-endpoi.nt/api.php\"\n")
		fmt.Fprintf(newConfig, "  secret: \"%s\"\n", Helper.RandStringBytes(50))

		fmt.Println("Created a new default config file. Please use the edit command to update it with your settings.")

	} else {
		fmt.Printf("Config file exists.: %s \n", ScientiaConfigFile)
		fmt.Println("Use 'read' to display or 'edit' to modify the config file.")
	}
}
