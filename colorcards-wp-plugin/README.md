# ColorCards Styler Plugin for WordPress

![current cycle: development](https://s3.amazonaws.com/cdn.shared/git-status-badges/current%20cycle-development-yellow.svg)

![deployable: no](https://s3.amazonaws.com/cdn.shared/git-status-badges/deployable--no-red.svg)

Colorcards is a plugin for WordPress that provides Shortcodes used to style posts and pages. The styles are designed to work well with the Parchment Paper theme and are geared toward making extremely long content more readable and less off-putting. This is mainly done by partitioning sections into a collapsible accordion.  

Although this current effort targets WordPress, the CSS and JS is applicable elsewhere.  

JavaScript, although not required, is used to allow sections to auto-un-collapse when navigated to by a target hash in the URL. Section's collapse states do not effect each other (you can have more than one open at a time) and with (soon-to-be) optional jQuery the states will be saved across user session via cookies.  

## Available Shortcodes

### Collapsable 

Wrap a section in `[collapsible title="Your Title"][/collapsible]` and the title be inside a clickable card above the content which is hidden by default until the card is clicked. You can use the value-less attribute `show` to make the content be uncollapsed when the page loads.  

__Collapsables With Colored Cards__  

By color the card will come from the theme unless you specify:  

    [collapsible title="Your Title" color=default]...[/collapsible]
    [collapsible title="Your Title" color=aqua]...[/collapsible]
    [collapsible title="Your Title" color=green]...[/collapsible]
    [collapsible title="Your Title" color=blue]...[/collapsible]
    [collapsible title="Your Title" color=orange]...[/collapsible]
    [collapsible title="Your Title" color=red]...[/collapsible]

`color=default` produces a gray card although eventually this will be made customizable from the end user. 

__The title Attribute__  

They title attribute does more than just set the text for the card, it also provides a way to link to the section, with the page scrolled down to it and un-collapsed. From the title an id is generated, which is all lowercase and with dashes instead of spaces. You should make each title be unique although if you have a duplicate, a number will be inserted at the end. Long titles may be truncated. If you have the `title="About Me"` you can append the url with `#about-me` and you will be taken to the section with it opened and viewable!  

__Reloading Pages while Retaining States__  

Normally when the viewer opens some sections and then reloads the page, the sections will not remember their states and will be collapsed, unless the shortcode has `open` or the url has a hash pointing to it. If jQuery is available these states will be retained via cookies.  


