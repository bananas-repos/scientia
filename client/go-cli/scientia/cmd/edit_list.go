package cmd

import (
	"fmt"
	"github.com/spf13/cobra"
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


// Subcommand of edit
// to list all available entries

var editListCmd = &cobra.Command {
	Use:   "list",
	Short: "List all available entries",
	Long:  "List all available entries",
	Run: func(cmd *cobra.Command, args []string) {
		listEntries()
	},
}

func init() {
	editCmd.AddCommand(editListCmd)
}

func listEntries() {
	if FlagVerbose {
		fmt.Println("Starting to request entries")
	}
}
