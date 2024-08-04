package cmd

import (
	"fmt"
	"github.com/spf13/cobra"
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

var editCmd = &cobra.Command {
	Use: "edit ID",
	Short: "Modify an entry by its ID",
	Long: "Edit an existing entry. Get the ID from the list command.",
	Run: func(cmd *cobra.Command, args []string) {
		var entryId string

		if len(args) == 1 {
			entryId = args[0]
		} else {
			cmd.Help()
			os.Exit(0)
		}

		response := getEndpointRequest("?p=entry&id=" + entryId);

		body := response.Data[0].Body
		ident := response.Data[0].Ident

		fh, err := os.CreateTemp("", ident)
		Helper.ErrorCheck(err, "Can not create tmp file for editing.")
		_, err = fmt.Fprintf(fh, body)

		// default editor
		var editor = "vim"

		if e := os.Getenv("VISUAL"); e != "" {
			editor = e
		} else if e := os.Getenv("EDITOR"); e != "" {
			editor = e
		}

		editCmd := exec.Command(editor, fh.Name())
		editCmd.Stdin = os.Stdin
		editCmd.Stdout = os.Stdout
		editCmd.Stderr = os.Stderr
		err = editCmd.Start()
		Helper.ErrorCheck(err, "Can not open tmp file")

		fmt.Println("Waiting for command to finish...")
		err = editCmd.Wait()
		Helper.ErrorCheck(err, "Command finished with error")
		fmt.Println("Done.")

		defer os.Remove(fh.Name())

	},
}

func init() {
	rootCmd.AddCommand(editCmd)
}
