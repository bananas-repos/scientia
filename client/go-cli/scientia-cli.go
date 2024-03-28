package main

import (
	"bytes"
	"encoding/json"
	"errors"
	"flag"
	"fmt"
	"io"
	"log"
	"math/rand"
	"net/http"
	"os"

	"gopkg.in/yaml.v2"
)

/**
 * scientia
 *
 * Copyright 2023 Johannes Keßler
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

const website = "https://www.bananas-playground.net/projekt/scientia/"
const version = "1.0"

// used for non-existing default config
const letters = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-_"

// command line parameters
var optsVerbose bool
var optsCreateConfig bool
var optsDebug bool

// config
var cfg Config

// config file struct
type Config struct {
	Endpoint struct {
		Host   string `yaml:"host"`
		Secret string `yaml:"secret"`
	} `yaml:"endpoint"`
}

// post json struct
type PayloadJson struct {
	Asl  string `json:"asl"`
	Data string `json:"data"`
}

// response json struct
type Response struct {
	Message string `json:"message"`
	Status  int    `json:"status"`
}

/**
 * Main
 */
func main() {

	// https://cli.urfave.org/v2/examples/arguments/
	// https://github.com/dnote/dnote
	//https://github.com/spf13/cobra/blob/main/site/content/user_guide.md

	// parse commandline parameters
	flag.BoolVar(&optsVerbose, "verbose", false, "Produce verbose output")
	flag.BoolVar(&optsCreateConfig, "create-config-file", false, "Create default config file")
	flag.BoolVar(&optsDebug, "debug", false, "Print debug infos")
	flag.Parse()
	if optsDebug {
		fmt.Println("verbose:", optsVerbose)
		fmt.Println("create-config-file:", optsCreateConfig)
		fmt.Println("debug:", optsDebug)
	}

	// load the config and populate Config
	loadConfig()

	// get the payload
	payload := getInput()
	if optsDebug {
		log.Println(payload)
	}

	// do the upload and get the response
	responseString := uploadCall(payload)
	response := Response{}
	json.Unmarshal([]byte(responseString), &response)

	// print the result and link to the pasty
	fmt.Printf("Status: %d\n", response.Status)
	fmt.Printf("Message: %s\n", response.Message)
}

/**
 * Check and display error with additional message
 */
func errorCheck(e error, msg string) {
	if e != nil {
		log.Fatal(msg, " ; Errrormsg: ", e)
	}
}

/**
 * just a random string
 */
func randStringBytes(n int) string {
	b := make([]byte, n)
	for i := range b {
		b[i] = letters[rand.Intn(len(letters))]
	}
	return string(b)
}

/**
 * load or even create a default config
 * $HOME/.scientia.yaml
 */
func loadConfig() {
	homeDir, err := os.UserHomeDir()
	errorCheck(err, "No $HOME directory available?")
	if optsVerbose {
		log.Printf("Your $HOME: %s \n", homeDir)
	}

	var configFile = homeDir + "/.scientia.yaml"

	if _, err := os.Stat(configFile); errors.Is(err, os.ErrNotExist) {
		log.Printf("Config file not found: %s \n", configFile)

		if optsCreateConfig {
			log.Printf("Creating new default config file: %s \n", configFile)

			newConfig, err := os.Create(configFile)
			errorCheck(err, "Can not create config file!")
			defer newConfig.Close()

			_, err = fmt.Fprintf(newConfig, "# scientia go client config file.\n")
			errorCheck(err, "Can not write to new config file")
			fmt.Fprintf(newConfig, "# See %s for more details.\n", website)
			fmt.Fprintf(newConfig, "# Version: %s\n", version)
			fmt.Fprintf(newConfig, "endpoint:\n")
			fmt.Fprintf(newConfig, "  host: http://your-scientia-endpoi.nt/api.php\n")
			fmt.Fprintf(newConfig, "  secret: %s\n", randStringBytes(50))

			log.Fatalf("New default config file created: - %s - Edit and launch again!", configFile)
		}
	}

	existingConfigFile, err := os.Open(configFile)
	errorCheck(err, "Can not open config file. Did you create one with -create-config-file?")
	defer existingConfigFile.Close()
	if optsVerbose {
		log.Printf("Reading config file: %s \n", configFile)
	}

	var decoder = yaml.NewDecoder(existingConfigFile)
	err = decoder.Decode(&cfg)
	errorCheck(err, "Can not decode config file")

	if cfg.Endpoint.Host == "" || cfg.Endpoint.Secret == "" {
		log.Fatal("Empty config?")
	}

	if optsDebug {
		log.Println(cfg.Endpoint.Host)
		log.Println(cfg.Endpoint.Secret)
	}
}

/**
 * Do a http POST call to the defined endpoint
 * and upload the payload
 * Return response body as string
 */
func uploadCall(payload string) string {

	if optsVerbose {
		log.Println("Starting to upload data")
	}
	if optsDebug {
		log.Println(payload)
	}
	if len(payload) == 0 {
		log.Fatal("Nothing provided to upload")
	}

	payloadStruct := PayloadJson{
		Asl:  cfg.Endpoint.Secret,
		Data: payload,
	}

	jsonData, err := json.Marshal(payloadStruct)
	errorCheck(err, "Can not create json payload")

	req, err := http.NewRequest(http.MethodPost, cfg.Endpoint.Host, bytes.NewBuffer(jsonData))
	errorCheck(err, "Can not create http request")
	// We need to set the content type from the writer, it includes necessary boundary as well
	req.Header.Set("Content-Type", "application/json; charset=UTF-8")
	req.Header.Set("User-Agent", "scientiaAgent/1.0")

	// Do the request
	client := &http.Client{}
	response, err := client.Do(req)
	errorCheck(err, "POST request failed")

	responseBody, err := io.ReadAll(response.Body)
	errorCheck(err, "Can not read response body")

	if optsVerbose {
		log.Println("Request done")
	}
	if optsDebug {
		log.Printf("Response status code: %d\n", response.StatusCode)
		log.Printf("Response headers: %#v\n", response.Header)
		log.Println(string(responseBody))
	}

	return string(responseBody)
}

/**
 * check if file is provided as commandline argument
 * or piped into
 * return the read data as string
 */
func getInput() string {
	if optsVerbose {
		log.Println("Getting input")
	}

	var inputString string

	if filename := flag.Arg(0); filename != "" {
		if optsVerbose {
			log.Println("Read from file argument")
		}

		bytes, err := os.ReadFile(filename)
		errorCheck(err, "Error opening file")
		inputString = string(bytes)
	} else {
		stat, _ := os.Stdin.Stat()
		if (stat.Mode() & os.ModeCharDevice) == 0 {
			if optsVerbose {
				log.Println("data is being piped")
			}

			bytes, _ := io.ReadAll(os.Stdin)
			inputString = string(bytes)
		}
	}

	if len(inputString) == 0 {
		log.Fatal("Nothing provided to upload")
	}

	return inputString
}
