package lib

import (
	"log"
	"math/rand"
)

const Version string = "1.0"
const Website string = "https://www.bananas-playground.net/projekt/scientia/"

// ErrorCheck if error then display it with an addition message
func ErrorCheck(e error, msg string) {
	if e != nil {
		log.Fatal(msg, " ; Message: ", e)
	}
}

// RandStringBytes creates just a random string
func RandStringBytes(n int) string {
	var letters = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-_"
	b := make([]byte, n)
	for i := range b {
		b[i] = letters[rand.Intn(len(letters))]
	}
	return string(b)
}
