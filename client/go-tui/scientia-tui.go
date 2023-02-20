/**
 * scientia
 *
 * A terminal client written in go.
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
package main

import (
	"fmt"
	"github.com/charmbracelet/bubbles/table"
	"github.com/charmbracelet/bubbles/textarea"
	"github.com/charmbracelet/bubbles/viewport"
	"github.com/charmbracelet/lipgloss"
	"os"

	"github.com/charmbracelet/bubbles/list"
	tea "github.com/charmbracelet/bubbletea"
)

// the unique identifiers for each action of the initial list of actions
const (
	ITEM_CREATE_VALUE = "create"
	ITEM_LIST_VALUE   = "list"
	ITEM_UPDATE_VALUE = "update"
)

// some global vars
var (
	quitTextStyle = lipgloss.NewStyle().Margin(1, 0, 1, 2)
)

// mainModel Holds all the important stuff
// Needs to be extended if a new action is added
type mainModel struct {
	start    list.Model
	create   textarea.Model
	list     table.Model
	choice   string
	quitting bool
	viewport viewport.Model
}

func (m mainModel) Init() tea.Cmd {
	return nil
}

// Update The main Update method. Decides the correct action update method
func (m mainModel) Update(msg tea.Msg) (tea.Model, tea.Cmd) {
	switch m.choice {
	case ITEM_UPDATE_VALUE:
		//return quitTextStyle.Render("Update it is")
	case ITEM_LIST_VALUE:
		return listUpdate(msg, m)
	case ITEM_CREATE_VALUE:
		return createUpdate(msg, m)
	}
	return startUpdate(msg, m)
}

// View The main View method. Decides which action view is called
func (m mainModel) View() string {
	if m.quitting {
		return quitTextStyle.Render("Good day.")
	}

	switch m.choice {
	case ITEM_UPDATE_VALUE:
		return quitTextStyle.Render("Update it is")
	case ITEM_LIST_VALUE:
		return listView(m)
	case ITEM_CREATE_VALUE:
		return createView(m)
	}

	return startView(m)
}

func main() {
	m := mainModel{start: initStart(), create: initCreate(), list: initList()}
	p := tea.NewProgram(m, tea.WithAltScreen())

	if _, err := p.Run(); err != nil {
		fmt.Println("Error running program:", err)
		os.Exit(1)
	}
}
