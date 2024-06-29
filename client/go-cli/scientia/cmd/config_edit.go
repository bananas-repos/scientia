package cmd

import (
	"errors"
	"fmt"
	"github.com/spf13/cobra"
	"log"
	"os"
	"os/exec"
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
 // to edit the config file

func init() {
	configCmd.AddCommand(configEditCmd)
}

var configEditCmd = &cobra.Command {
	Use:   "edit",
	Short: "Edit config file",
	Long:  "Edit the config file with $VISUAL > $EDITOR",
	Run: func(cmd *cobra.Command, args []string) {
		editConfig()
	},
}

func editConfig() {
	// default editor
	var editor = "vim"

	if e := os.Getenv("VISUAL"); e != "" {
		editor = e
	} else if e := os.Getenv("EDITOR"); e != "" {
		editor = e
	}

	if FlagDebug {
		fmt.Printf("DEBUG Local user config path: %s\n", ScientiaConfigPath)
		fmt.Printf("DEBUG Local user config file: %s\n", ScientiaConfigFile)
		fmt.Printf("DEBUG Using editor: %s\n", editor)
	}

	if _, err := os.Stat(ScientiaConfigFile); errors.Is(err, os.ErrNotExist) {
		log.Fatal("Config file missing.");
	}

	cmd := exec.Command(editor, ScientiaConfigFile)
	cmd.Stdin = os.Stdin
	cmd.Stdout = os.Stdout
	cmd.Stderr = os.Stderr
	err := cmd.Start()
	Helper.ErrorCheck(err, "Can not open config file")

	fmt.Println("Waiting for command to finish...")
	err = cmd.Wait()
	Helper.ErrorCheck(err, "Command finished with error")
	fmt.Println("Done.")
}
