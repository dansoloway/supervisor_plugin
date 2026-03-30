Supervisor Plugin — Design Specification

Source: Approved redesign mockups (PDF).
Purpose: Provide a clear UI reference so AI tools and developers can implement consistent styling across the plugin.

1. Global Design Principles

The redesign aims to modernize the interface while keeping the current structure and functionality intact.

Core goals:

cleaner visual hierarchy

more whitespace

clearer section structure

consistent typography

card-based content blocks

modern search and filter UI

better responsive behavior

maintain full RTL support

Design philosophy:

simplicity

readability

consistent spacing rhythm

minimal visual noise

clear grouping of related content

Functional behavior must remain unchanged.

2. Global UI Rules
Layout

Pages should follow a consistent structure:

page header
page intro (optional)
primary content block
secondary blocks
footer/navigation

Spacing between sections should feel consistent and generous.

Recommended spacing hierarchy:

section gap > block gap > inline gap

Containers should avoid overly wide text areas for readability.

Typography

Typography hierarchy:

H1  page title
H2  major section title
H3  subsection title
H4  smaller headers

Body text
Muted text (dates, metadata)

Paragraphs should have comfortable line height and spacing.

Text alignment should respect RTL layout.

Links

Two types of links appear:

Standard internal links

Styled according to the general site style.

External or source links

External references should:

use traditional blue color (#0000EE)

be clearly identifiable

optionally underline on hover

Examples:

bibliography links

research sources

organization websites

reports

3. Shared UI Components
Cards

Many elements should visually behave like cards.

Card characteristics:

rounded corners
soft border
comfortable padding
clear vertical spacing

Cards are used for:

updates

stories

organization entries

search results

Buttons

Buttons should share a consistent base style.

Used for:

search

filters

knowledge map navigation

"more updates"

content expansion

Buttons should:

have comfortable padding

clear hover state

maintain good contrast

Inputs

Inputs include:

search fields

filter inputs

Design requirements:

rounded borders
clear focus state
comfortable padding
consistent height

Search fields should visually stand out from surrounding content.

4. Homepage

The homepage includes several major components.

Hero / Welcome Section

The page begins with a welcoming title and short description.

Layout:

large page title
short description text
centered layout
comfortable spacing

The goal is to orient the user quickly.

Knowledge Map

The knowledge map is one of the central navigation elements.

It consists of several topic blocks.

Examples include:

גוף פיקוח

רכש חברתי

מדינת הרווחה הרגולטורית

שיטות עבודה

פיתוח ידע

מדיניות והדרכה

בקרה ואכיפה

Design requirements:

grid layout
even spacing
square or nearly square blocks
rounded corners
clear visual grouping

Each block acts as a navigation element.

Hover states should make blocks feel interactive.

Search Section

A search interface allows users to search across knowledge resources.

Search design:

large search input
search icon inside the field
clear placeholder text
comfortable padding
rounded border

Search should visually feel like a primary interaction.

Updates Section

The updates section shows recent updates.

Each update entry contains:

title
date
short description

Updates should appear as structured content blocks.

Design characteristics:

clear separation between updates
consistent spacing
strong title hierarchy

A button or link should lead to more updates.

Stories / Featured Content

This section displays stories or highlighted content.

Each story should appear as a card:

image
title
short text

Cards should:

have consistent heights
have comfortable spacing
be visually balanced
5. Study Case / Article Page

This page displays a single study case or article.

Structure:

breadcrumb (optional)
title
content
image

Design goals:

comfortable reading width
good paragraph spacing
clear title hierarchy
balanced image placement

On smaller screens the image should stack naturally with the text.

6. Search Results Page

Search results appear when users search for content.

Each result should display:

icon or visual indicator
title
date
short description

Design requirements:

clear separation between results
strong hierarchy
consistent spacing

Results should be easy to scan.

7. Updates Page

The updates page contains filters and update entries.

Layout:

filter panel
updates list

Filters include categories such as:

תחומים
תמות
נושאי מפתח

Filter design:

clear group titles
well spaced checkboxes
clean search field

Updates should use the same visual language as homepage update cards.

8. Knowledge Map Page

This page explains the knowledge map concept and provides navigation.

Structure:

intro text
knowledge map content
key topics

Design goals:

clear informational layout
good readability
logical spacing between sections
9. Key Topics Page

This page displays key topics.

Topics include:

רכש חברתי
בקרה עצמית
מדיניות פיקוח
בקרה חיצונית
סטנדרטים לאיכות השירותים
אכיפה מתקנת ואכיפה עונשית
מדינת רווחה רגולטורית
שיתוף מקבלי השירות בפיקוח
חומרי הדרכה
מחקרים
שקיפות והנגשת מידע
יחסי מפקחים מפוקחים
פיקוח משולב
ניהול סיכונים

Layout:

icon grid
icon + label
consistent card size

Each topic acts as a navigation element.

Hover states should indicate interactivity.

10. Research / Bibliography Page

This page lists research sources.

Entries contain:

author
title
year
link

Design goals:

clean bibliography layout
good spacing
readable long titles

External links should be clearly styled.

11. Supervision Bodies Page

This page lists international supervision organizations.

Each entry contains:

flag
organization name
domain (health / welfare / education)

Design:

grid of cards
flag icon
organization name
category labels

Cards should align consistently.

12. Organization Detail Page

This page displays information about a single organization.

Structure:

title
metadata panel
description text
related content

Metadata panel may include:

organization website
reports
country
sector

Design requirements:

clear grouping of metadata
clean link styling
comfortable reading layout
13. Responsive Behavior

The layout must work across screen sizes.

Key behaviors:

cards stack vertically
grids reduce column count
filters move above results
images stack with text

Touch targets should remain easy to tap.