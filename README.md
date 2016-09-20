# Jeffpack for WordPress

![current cycle: development](https://s3.amazonaws.com/cdn.shared/git-status-badges/current_cycle-development-yellow.svg)  

![deployable: partially](https://s3.amazonaws.com/cdn.shared/git-status-badges/deployable-partially-yellow.svg)  

Jeffpack for WordPress is a undeniably self-serving project but perhaps you'll find it useful. It's the code and the means I use to set up a new WordPress site. The code is (right now) a theme and a plugin. The means is SSH and git on the server. `cd` to the root of the WordPress site and pull various branches in without touching the WordPress Core code. The `.gitignore` is more like a "git DON"T ignore" in that it whitelists the code from this repository, or your fork of it and doesn't touch anything else unless you specify. 


The Jeffpack code is a WordPress Plugin / Theme pair for styling your WordPress sites and individual posts via Shortcodes. The plugin contains much of the functional part of CSS and JavaScript whereas the Theme fills in more visual details capturing the feel of naturally aged paper. Textures and shadows are everywhere but are so subtle they are nearly subliminal.  

Future plans involve expanding to a modular set Themes and Plugins all working with the same set of CSS classes. Each Theme can work with any Plugin and visa versa, where the choice of theme will be a way to change to alternate coloring and layout options.  

Refer the the individual README.md's in component's directory for further information.  