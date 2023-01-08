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
 * This is the start "screen" which displays the available actions which can be selected.
 * The values and identifiers are defined in the main file, since they are used there too
 */
package main

import (
	"fmt"
	"github.com/charmbracelet/bubbles/list"
	tea "github.com/charmbracelet/bubbletea"
	"github.com/charmbracelet/lipgloss"
	"io"
)

var (
	titleStyle        = lipgloss.NewStyle().MarginLeft(0)
	itemStyle         = lipgloss.NewStyle().PaddingLeft(4)
	selectedItemStyle = lipgloss.NewStyle().PaddingLeft(2).Foreground(lipgloss.Color("170"))
	paginationStyle   = list.DefaultStyles().PaginationStyle.PaddingLeft(4)
	helpStyle         = list.DefaultStyles().HelpStyle.PaddingLeft(4).PaddingBottom(1)
)


// item stuff
type item struct {
	title, desc string
}
func (i item) Title() string       { return i.title }
func (i item) Description() string { return i.desc }
func (i item) FilterValue() string { return i.title }

type itemDelegate struct{}

func (d itemDelegate) Height() int {
	return 1
}
func (d itemDelegate) Spacing() int {
	return 0
}
func (d itemDelegate) Update(msg tea.Msg, m *list.Model) tea.Cmd {
	return nil
}
func (d itemDelegate) Render(w io.Writer, m list.Model, index int, listItem list.Item) {
	i, ok := listItem.(item)
	if !ok {
		return
	}

	str := fmt.Sprintf("%d. %s", index+1, i.Title())

	fn := itemStyle.Render
	if index == m.Index() {
		fn = func(s string) string {
			return selectedItemStyle.Render("> " + s)
		}
	}

	fmt.Fprint(w, fn(str))
}

func initStart() list.Model {
	items := []list.Item {
		item{title: "Create", desc: ITEM_CREATE_VALUE},
		item{title: "List", desc: ITEM_LIST_VALUE},
		item{title: "Update", desc: ITEM_UPDATE_VALUE},
	}

	l := list.New(items, itemDelegate{}, 20, 14)
	l.Title = "Please select an option"
	l.SetShowStatusBar(false)
	l.SetFilteringEnabled(false)
	l.Styles.Title = titleStyle
	l.Styles.PaginationStyle = paginationStyle
	l.Styles.HelpStyle = helpStyle

	return l
}

func startView(m mainModel) string {
	return fmt.Sprintf("\n%s", m.start.View())
}

func startUpdate(msg tea.Msg, m mainModel) (tea.Model, tea.Cmd) {
	switch msg := msg.(type) {
	case tea.WindowSizeMsg:
		m.start.SetWidth(msg.Width)
		return m, nil

	case tea.KeyMsg:
		switch msg.Type {
			// esc does close?
			case tea.KeyEsc:
				return m, nil
			case tea.KeyCtrlC:
				m.quitting = true
				return m, tea.Quit

			case tea.KeyEnter:
				i, ok := m.start.SelectedItem().(item)
				if ok {
					m.choice = i.Description()
				}
				return m, nil

			case tea.KeyRunes:
				switch string(msg.Runes) {
					case "q":
						m.quitting = true
						return m, tea.Quit
					}
			}
	}

	var cmd tea.Cmd
	m.start, cmd = m.start.Update(msg)
	return m, cmd
}
