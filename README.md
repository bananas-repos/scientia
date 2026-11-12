# scientia

+ *scientia* - cognizance, insight, knowledge
+ *scientia utilis* - useful knowledge

https://www.bananas-playground.net/projekt/scientia/

A self hosted small note collection application. Simple storage of text into the database. Browsable by date.  
Searchable with mysql fulltext search. Protected by BASIC AUTH, single user only, so far.

# Usage

Install it and access it with a browser.

After login you can browse and search every note. They are sorted by date and grouped by year. Selecting a year
does limit the view to this year only. After selecting an entry you are able to filter by day and month by using the `Breadcrumb`.

Adding, with the action `New`,  does create it with the current date and creates a uniq url.

Editing by selecting a note and saving the changes. It updates the search database and the updated timestamp.

To delete an entry you have to select it and check the `Delete entry` option. Click `Save` and the entry is gone.

# Search

The search is a mysql fulltext search. It builds an index by using the words provided in the entries. Everything longer then 3 chars is added
to the index. MySQL has its own [ignore/stop words](https://dev.mysql.com/doc/refman/26.10/en/fulltext-stopwords.html) build in.

Using the search at the startpage, the results come frome everthing. Limiting the result to a maximum of 100.

Searching withing a year or month, the results are limited to this timeframe.

# Requirements

+ A Webserver (tested on Apache 2.4) 
+ PHP 8+ with mysqli, mbstring, curl 
+ MySQL 8+ / MariaDB 10.2

# Technical information

This is build and tested on a linux based OS. Windows support not yet, but it could work.

# Contribute

Want to contribute or found a problem?

See Contributing document: CONTRIBUTING.md

# Uses

See uses document: USES
