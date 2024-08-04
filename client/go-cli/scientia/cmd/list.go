package cmd

import (
	"encoding/json"
	"fmt"
	"github.com/spf13/cobra"
	"io"
	"net/http"
	Helper "scientia/lib"
	"strings"
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


// to list all available entries

var listCmd = &cobra.Command {
	Use:   "list",
	Short: "List all available entries",
	Long:  "List all available entries with its ID, date and content preview.",
	Run: func(cmd *cobra.Command, args []string) {
		listEntries()
	},
}

func init() {
	rootCmd.AddCommand(listCmd)
}

func listEntries() {
	response := getEndpointRequest("")

	for _, entry := range response.Data {
		leftOfDelimiter, _, _ := strings.Cut(entry.Body, "\n")
		fmt.Println(entry.Ident, entry.Date, leftOfDelimiter)
	}
}


func getEndpointRequest(params string) GetResponse {
	if FlagVerbose {
		fmt.Println("Starting to request get endpoint")
	}

	req, err := http.NewRequest(http.MethodGet, ScientiaConfig.Endpoint.Get + params, nil)
	Helper.ErrorCheck(err, "Can not create http request")
	// We need to set the content type from the writer, it includes necessary boundary as well
	req.Header.Set("User-Agent", "scientiaAgent/1.0")
	req.Header.Set("X-ASL", ScientiaConfig.Endpoint.Secret)

	// Do the request
	client := &http.Client{}
	response, err := client.Do(req)
	Helper.ErrorCheck(err, "GET request failed")

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

	returnResponse := GetResponse{}
	if response.StatusCode != 200 {
		returnResponse = GetResponse {
			Message: "Status not as expected.",
			Status: response.StatusCode,
			Data: []GetResponseEntry{}}
	} else {
		err := json.Unmarshal([]byte(responseBody), &returnResponse)
		Helper.ErrorCheck(err, "Can not parse return json")
	}

	return returnResponse
}
