package cmd

import (
	"bytes"
	"encoding/json"
	"fmt"
	"github.com/spf13/cobra"
	"io"
	"log"
	"net/http"
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

func init() {
	rootCmd.AddCommand(addCmd)
}

var addCmd = &cobra.Command {
	Use: "add file.ext",
	Short: "Add a new entry and get the URL returned",
	Long: "Add a new entry based on a file or piped cat file | scientia add. Returns the url to the new entry.",
	Run: func(cmd *cobra.Command, args []string) {
		// check if there is a file or piped content
		var inputString string

		if len(args) == 1 {
			if FlagVerbose {
				fmt.Println("Read from file argument")
			}
			bytes, err := os.ReadFile(args[0])
			Helper.ErrorCheck(err, "Error opening file")
			inputString = string(bytes)
		} else if stat, _ := os.Stdin.Stat(); (stat.Mode() & os.ModeCharDevice) == 0  {
			if FlagVerbose {
				fmt.Println("Read from piped stdin")
			}
			bytes, _ := io.ReadAll(os.Stdin)
			inputString = string(bytes)
		} else {
			cmd.Help()
			os.Exit(0)
		}

		response := upload(inputString)

		// print the result and link to the pasty
		fmt.Printf("Status: %d\n", response.Status)
		fmt.Printf("Message: %s\n", response.Message)
	},
}

type PayloadJson struct {
	Asl  string `json:"asl"`
	Data string `json:"data"`
}

type Response struct {
	Message string `json:"message"`
	Status  int    `json:"status"`
}

// Upload the given data and return a Response struct
func upload(payload string) Response {
	if FlagVerbose {
		fmt.Println("Starting to upload data")
	}
	if FlagDebug {
		fmt.Println("DEBUG print payload:\n" + payload)
	}
	if len(payload) == 0 {
		log.Fatal("Nothing provided to upload?")
	}

	payloadStruct := PayloadJson {
		Asl:  ScientiaConfig.Endpoint.Secret,
		Data: payload,
	}

	jsonData, err := json.Marshal(payloadStruct)
	Helper.ErrorCheck(err, "Can not create json payload")

	req, err := http.NewRequest(http.MethodPost, ScientiaConfig.Endpoint.Add, bytes.NewBuffer(jsonData))
	Helper.ErrorCheck(err, "Can not create http request")
	// We need to set the content type from the writer, it includes necessary boundary as well
	req.Header.Set("Content-Type", "application/json; charset=UTF-8")
	req.Header.Set("User-Agent", "scientiaAgent/1.0")

	// Do the request
	client := &http.Client{}
	response, err := client.Do(req)
	Helper.ErrorCheck(err, "POST request failed")

	responseBody, err := io.ReadAll(response.Body)
	Helper.ErrorCheck(err, "Can not read response body")

	if FlagVerbose {
		fmt.Println("Request done")
	}
	if FlagDebug {
		fmt.Printf("DEBUG Response status code: %d\n", response.StatusCode)
		fmt.Printf("DEBUG Response headers: %#v\n", response.Header)
		fmt.Println("DEBUG Response body:\n", string(responseBody))
	}

	returnResponse := Response{}
	if response.StatusCode != 200 {
		returnResponse = Response {
			Message: "Invalid response status. Use --debug for more information.",
			Status: response.StatusCode }
	} else {
		err := json.Unmarshal([]byte(responseBody), &returnResponse)
		Helper.ErrorCheck(err, "Can not parse return json")
	}

	return returnResponse
}
