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
 *
 *
 * This is the create "screen". It displays a textarea to input text into.
 * Does the save and creation
 */
package main

import (
	"github.com/charmbracelet/bubbles/textarea"
	tea "github.com/charmbracelet/bubbletea"
	"github.com/charmbracelet/lipgloss"
)

var (
	headline = lipgloss.NewStyle().Margin(1, 0, 1, 2)
	infoText = lipgloss.NewStyle().Margin(1, 0, 1, 2).Foreground(lipgloss.AdaptiveColor{Light: "#969B86", Dark: "#696969"})
)

func initCreate() textarea.Model {
	ta := textarea.New()
	ta.Placeholder = "Once upon a time..."
	ta.SetHeight(10)
	ta.SetWidth(80)
	ta.Focus()

	return ta
}

func createView(m mainModel) string {
	return lipgloss.JoinVertical(lipgloss.Left,
		headline.Render("Create a new entry"),
		m.create.View(),
		infoText.Render("esc*2 to get back and discard. ctrl+s to save."))
}

func createUpdate(msg tea.Msg, m mainModel) (tea.Model, tea.Cmd) {

	var cmds []tea.Cmd
	var cmd tea.Cmd

	switch msg := msg.(type) {

	case tea.WindowSizeMsg:
		m.create.SetWidth(msg.Width)
		return m, nil

	case tea.KeyMsg:
		switch msg.Type {
		case tea.KeyCtrlC:
			m.quitting = true
			return m, tea.Quit
		case tea.KeyCtrlS:
			m.choice = ""
			return m, nil

		case tea.KeyEsc:
			if m.create.Focused() {
				m.create.Blur()
			} else if !m.create.Focused() {
				m.choice = ""
				return m, nil
			}
		default:
			if !m.create.Focused() {
				cmd = m.create.Focus()
				cmds = append(cmds, cmd)
			}
		}
	}

	m.create, cmd = m.create.Update(msg)
	cmds = append(cmds, cmd)
	return m, tea.Batch(cmds...)
}
