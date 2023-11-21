# Contributing to nmrXiv Documentation

This website is based on the [Vitepress framework](https://vitepress.dev/guide/what-is-vitepress) which is a [Static Site Generator (SSG)](https://en.wikipedia.org/wiki/Static_site_generator) designed for building fast, content-centric websites.
To put it briefly, VitePress processes your source content written in [Markdown](https://www.markdownguide.org/), applies a theme, and produces static [HTML](https://html.com/) pages that can be effortlessly deployed on any platform.

::: info 
For more information on Markdown extensions/formats used by Vitepress follow the documentation [here](https://vitepress.dev/guide/markdown)
:::

## Forking
First, you need to fork the [nmrXiv repository on GitHub](https://github.com/NFDI4Chem/nmrxiv)

## Installation 
Open the forked repo in your local in the editor of your choice such as [Visual Studio Code](https://code.visualstudio.com/) and follow the below steps to start with:

```bash
    npm install
```
```bash
    npm run docs:dev
```

Your local documentation site is ready and would be running on http://localhost:5173/

## Branching
Whenever you want to edit a page, please do the following:
* Create a new branch for that page from the "development" branch.
* Make your modifications on that branch.
* Create a pull request on GitHub for your fork to "development".

Afterwards, the maintainers will review your changes and merge them.

## How to 

### Adding a New Page
* Go to the docs directory located at root location.
* Add a new file there or folder.
* Name your file complying with our conventions (kebab-case-of-small-letters).
* Give it the extension ".md".
* Configure the newly added file to `docs/.vitepress/config.mjs`.

### Headings
To create a heading, add one or more # signs in front of the word or phrase. The number of # you use should correspond to the heading level. Many # indicates a low level.

### Escaping Signs
To show signs specified in the Markdown syntax, escape them with "\".

### Links
Links can be added via [Markdown](https://www.markdownguide.org/):

In Markdown:
```md
[Text to be shown on the page](actual link)
```
### Lists
#### Ordered List
Enter the number you want your list to start with, enter a dot and a space, then enter your text. When you hit enter, the next number will be generated automatically.
```
2. First line.
3. Second line.
4. Third line.
```
Output:

2. First line.
3. Second line.
4. Third line.

Please note, if you enter random numbers with dots, the generated list will still be ordered based on the first number!
```
2. First line.
5. Second line.
2. Third line.
```

Output:

2. First line.
5. Second line.
2. Third line.


#### Unordered Lists:
Use either * or - at the beginning of the line, then enter a space and then your text.
```
* First line.
- Second line.
```

Output:
* First line.
- Second line.

### Code Blocks
Code blocks enable highlighting your code and its syntax based on the language. To use code blocks:
1. Wrap your code with three backticks.
2. Add the right language.
3. Insert your code.

For instance: 

\```js

<span style="color:yellow;">Example</span>

\```

Which will look like this: 

```js
<span style="color:yellow;">Example</span>
```

For a list of the supported languages, please visit the [link](https://github.com/FormidableLabs/prism-react-renderer/blob/master/src/vendor/prism/includeLangs.js). It is common to use "bash", "html", "js", and "md" in this documentation. Please use "Bash" code blocks for errors too.

### Images/gifs

Images/gifs can be inserted in a centered mode with a specified width using HTML. Please use the following block and change the path to your image, along with the width and caption. If you have many images related to one topic, please group them in one folder. All images are to be added to `/static/img` folder.

```html
<p align="center">
<img src="/img/project/view.png" width="1000"/>
<figcaption>Private project view</figcaption>
</p>
```

#### SVG
SVGs need special treatment. First import them as [React component](https://reactjs.org/react-component.html), and then insert them in your text with `className="logo"`:

``` js
import Sun from '@site/static/img/sun.svg'
<Sun title="Sun" className="logo" width={"30px"} height={"30px"} />
```
### Tables
```
| Header 1 | Header 2 |
|---|---|
| Field 1  |  Field 2 |
```
Output:

| Header 1 | Header 2 |
|---|---|
| Field 1  |  Field 2 |

### Info Boxes
Custom containers can be defined by their types, titles, and contents.

```
::: info
This is an info box.
:::

::: tip
This is a tip.
:::

::: warning
This is a warning.
:::

::: danger
This is a dangerous warning.
:::

::: details
This is a details block.
:::
```

::: info
This is an info box.
:::

::: tip
This is a tip.
:::

::: warning
This is a warning.
:::

::: danger
This is a dangerous warning.
:::

::: details
This is a details block.
:::

### Inserting Downloadable Files
If you want to provide one or multiple file(s) to be downloaded directly from the documentation, upload them to the [files folder in GitHub](https://github.com/NFDI4Chem/nmrxiv-docs/tree/development/static/files), and insert a Download Button with the file path(s), separated by a comma, in `files` and the text to be shown on the button in `text`:

```
import { DownloadBtn } from '@site/download-files.js'

<DownloadBtn files={['/nmrxiv-docs/static/files/test-file.txt']}  text={"Download test file here"}/>
```

### Formatting

#### Bold

```
<b>Example</b>
```
Output:
<b>Example</b>

#### Italics
```
<i>Example</i>
```
Output:
<i>Example</i>

#### Highlighted Elements

```
`Example`
```
Output:
`Example`

#### Line Breaks
Add two blank lines (two "enter"s) where the line break is needed to be.

#### Color
* Colored letters:
```js
<span style="color:yellow;">Example</span>
```
Output:
<span style="color:yellow;">Example</span>

* Colored background:
```js
<span style="background:yellow;">Example</span>
```

Output:
<span style="background:yellow;">Example</span>

#### Strikethrough
```html
<del>Example</del>
```
Output:
<del>Example</del>


## Conventions

### Hyperlinks
We would recommend the intensive use of hyperlinks instead of plain text whenever possible. The intro of this page is a good example of that. 

### Bold Text
All bolded terms in this documentation can also be found within the user/developer interface. 

### Tables
To maintain a consistent view throughout the table, the first letter from the first word will always be capitalized (unless it is a unit). Full stops will be used when the correct language requires them. The following table provides more details and examples.

| Case | Capitalization | Full stop | Example |
|---|---|---|---|
| Unit | to be written as provided by [The International System of Units (SI)](https://www.bipm.org/en/measurement-units) | No | "Hz", "°C", "cm" |
| Single word| Only first letter, unless the word is usually written differently| No | "No", "True", "NMR" |
| Phrase | Only first letter from the first word, unless other words require so | No | "NMR technique", "Chemical formula" |
| Sentence | To be written according to the correct use of language | Yes | "Fields with description are usually provided as sentences which requires adding a full stop at the end of the sentence." |

### Capitalization
Please always capitalize:
* The first letter of the first word, unless it is officially written with small letters, e.g., "nmrXiv".
* Headings: The first letter of each word except articles, conjunctions, and prepositions with less than four letters. e.g., "Contributing to nmrXiv Docs".

Please note: Prepositions are dealt with as normal words and are capitalized when applicable in the following cases:
* If they are part of the verb: "Starting Up the Application".
* If they have more than four letters: "Further Steps After Installation".

### Highlighting Elements
[Highlighted Elements](https://docs.nmrxiv.org/contributing/contributing-to-nmrxiv-docs/#highlighted-elements) should be used with files and folders names, and to highlight words where bolding is not applicable.

### Images
Images are to be added directly after the corresponding paragraph.

### Citations
When citing nother resource, please add a number at the end of the cited section within brackets. For instance: "This is a cited line.[1]". At the bottom of the page, pleas add a new paragraph highlighted with one "\#" and name it as "References". Then provide the numbered list of references as persistent links.
